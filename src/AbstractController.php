<?php

namespace Maduser\Laravel\Resource;

use App\Http\Controllers\Controller;
use Exception;
use Illuminate\Contracts\Support\Htmlable;
use Illuminate\Database\QueryException;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Cookie;
use Maduser\Laravel\Resource\Fields\AbstractField;
use Maduser\Laravel\Resource\Fields\BelongsToMany;
use Maduser\Laravel\Resource\Contracts\ResourceInterface;
use Maduser\Laravel\Resource\Views\ResourceDisplay;
use Maduser\Laravel\Resource\Views\ResourceTable;
use Maduser\Laravel\ViewModel\Container;
use Maduser\Ui\Blade\Dashmix\Views\Block\Block;
use Maduser\Ui\Blade\Dashmix\Views\ButtonLink;
use Maduser\Ui\Blade\Dashmix\Views\Block\FormBlock;
use Maduser\Ui\Blade\Dashmix\Views\Page;
use Maduser\Ui\Blade\Dashmix\Views\Table;
use ReflectionException;
use Throwable;

/**
 * Class ResourceController
 *
 * TODO: Create services for the clumsy stuff
 *
 * @package Maduser\Laravel\Resource\Resource
 */
class AbstractController extends Controller
{
    /**
     * The resource this controller will interact with
     *
     * @var ResourceInterface
     */
    protected $resource;

    /**
     * The page title in the view
     *
     * @var string
     */
    protected $pageTitle = 'Resources';

    /**
     * Gets the page title for the view
     *
     * @return string
     */
    public function getPageTitle(): string
    {
        return $this->pageTitle;
    }

    /**
     * Sets the page title for the view
     *
     * @param  string  $pageTitle
     *
     * @return ResourceController
     */
    public function setPageTitle(string $pageTitle): ResourceController
    {
        $this->pageTitle = $pageTitle;
        return $this;
    }

    /**
     * ResourceController constructor.
     *
     * @param ResourceInterface $resource
     */
    public function __construct(ResourceInterface $resource)
    {
        $this->resource = $resource;
    }

    /**
     * Display a listing of the resource.
     *
     * @return Htmlable|Page
     * @throws ReflectionException
     * @throws Throwable
     */
    public function index()
    {
        $userOrder = $this->getUserOrder();
        $userFilters = $this->getUserFilters();

        // Set order and filters on the resource
        $this->resource->orderBy($userOrder);
        $this->resource->filterBy($userFilters);

        // Make a paginated table
        $table = ResourceTable::create(['resource' => $this->resource]);
        $table->setRows($this->resource->paginate());
        $table->setOrder($userOrder);
        $table->setFilters($userFilters);

        // Make a block which holds that table
        $block = Block::create([
            'title' => $this->resource->getTitle(),
            'content' => $table->render()
        ]);

        // Make a create button to place in the top right region of the page
        $buttonCreate = ButtonLink::create([
            'text' => __('Create new ' . $this->resource->getLabel()),
            'url' => route('resource.create', [$this->resource->getName()]),
            'icon' => 'fa fa-plus-circle',
            'btnClass' => 'btn btn-info',
            'wrapperClass' => ''
        ]);

        // Make the page with the create widgets and return it's view
        return Page::create([
            'title' => $this->getPageTitle(),
            'regionTopRight' => $buttonCreate,
            'widgets' => $block
        ]);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return Htmlable|Page
     * @throws ReflectionException
     */
    public function create()
    {
        // Render the fields of the resource
        $container = Container::create();
        $this->resource->getFields()->each(function(AbstractField $field) use ($container) {
            $field->isNotInContext(['form']) || $container->push($field);
        });

        // Make a Form that wraps the rendered fields
        $formBlock = FormBlock::create([
            'title' => $this->resource->getLabel(),
            'name' => $this->resource->getName(),
            'content' => $container,
            'cancelUrl' => route('resource.index', [$this->resource->getName()]),
            'action' => route('resource.store', [
                'resource' => $this->resource->getName()
            ]),
        ]);

        // Make the page with the created widgets and return it's view
        return Page::create([
            'title' => $this->getPageTitle(),
            'widgets' => $formBlock
        ]);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param Request $request
     *
     * @return Response|RedirectResponse
     * @throws ReflectionException
     * @throws Exception
     */
    public function store(Request $request)
    {
        // Set the Kpi record from user inputs and validate them
        $validator = $this->resource->setValues($request->all())->validate();

        // Return to the form if validation has failed
        if ($validator->fails()) {
            return redirect()
                ->to(url()->previous())
                ->with('inputs_' .$this->resource->getName(), $request->all())
                ->withErrors($validator, $this->resource->getName());
        }

        // Save the resource
        $this->resource->save();

        // Redirect to the show resource page
        return redirect()
            ->route('resource.show', [$this->resource->getName(), $this->resource->getId()])
            ->with($this->resource->getName() . '.success', 'Data saved');
    }

    /**
     * Update the specified resource in storage.
     *
     * @param Request $request
     *
     * @return RedirectResponse
     * @throws ReflectionException
     * @throws Exception
     */
    public function update(Request $request)
    {
        $id = $request->get($this->resource->getIdentifier());
        $resource = $this->resource->find($id);

        // Set the Kpi record from user inputs and validate them
        $validator = $resource->setValues($request->all())->validate();

        // Return to the form if validation has failed
        if ($validator->fails()) {
            return redirect()
                ->to(url()->previous())
                ->with('inputs_'.$resource->getName(), $request->all())
                ->withErrors($validator, $resource->getName());
        }

        // All good so far...
        $resource->save();

        // Probably ok!
        return redirect()
            ->route('resource.show', [$resource->getName(), $id])
            ->with($resource->getName().'.success', 'Data saved');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param Request $request
     *
     * @return Response|RedirectResponse
     * @throws ReflectionException
     * @throws Exception
     */
    public function destroy(Request $request)
    {
        // Find the resource
        $id = $request->get($this->resource->getIdentifier());
        $resource = $this->resource->find($id);

        // Get the value of the label field now, as it won't be available after
        // deletion
        $label = $resource->getField($resource->getLabelField())->getValue();

        // Delete the resource
        try {

            $resource->delete();

        } catch (QueryException $e) {

            // Handle "Integrity constraint violation" ...
            if ($e->getCode() == '23000') {
                return redirect()
                    ->route('resource.show', [$resource->getName(), $id])
                    ->with(
                        $this->resource->getName().'.error',
                        sprintf(
                            '%s "%s" cannot be deleted as other objects depend on it',
                            $resource->getLabel(),
                            $label
                        )
                    );
            }

            // ...or throw the QueryException
            throw new QueryException(
                $e->getSql(), $e->getBindings(), $e->getPrevious()
            );
        }

        // Redirect with success message and info
        return redirect()
            ->route('resource.index', [$this->resource->getName()])
            ->with(
                $this->resource->getName() . '.success',
                $this->resource->getLabel() . ' "' . $label . '" deleted'
            );
    }

    public function reorder(Request $request, string $resource, int $id)
    {
        // Load the given resource
        $resource = $this->resource->findOrFail($id);
        $method = $resource->getField('fields')->getMethod();

        $items = $request->post('items');

        collect($items)->each(function($item, $index) use ($resource, $method) {
            $resource->getModel()->{$method}()->updateExistingPivot($item, ['delta' => $index]);
        });

    }

    /**
     * Retrieve the order parameter from GET or COOKIE
     *
     * @return array|null
     * @throws ReflectionException
     */
    protected function getUserOrder(): ? array
    {
        $parameterName = $this->resource->getNamespace('_order');

        // Prioritize GET-parameter
//        if ($parameter = request()->query($parameterName)) {
//            return $this->parseUserOrderGet($parameter);
//        }

        // No GET, then try cookie
        $cookie = json_decode(Cookie::get('resources'), true);
        if (isset($cookie[$this->resource->getName()])) {
            return $this->parseUserOrderCookie($cookie[$this->resource->getName()]);
        }

        return [];
    }

    /**
     * @param  string  $parameter
     *
     * @return array
     */
    protected function parseUserOrderGet(string $parameter): array
    {
        $e = explode('.', $parameter);
        $field = $e[0];
        $direction = isset($e[1]) ? $e[1] : 'asc';

        return [$field => $direction];
    }

    /**
     * @param  array  $cookie
     *
     * @return array
     */
    protected function parseUserOrderCookie(array $cookie): array
    {
        $field = null;
        $direction = null;

        if (isset($cookie['order']) && !empty($cookie['order'])) {
            $field = $cookie['order']['field'];
            $direction = $cookie['order']['direction'];

            return [$field => $direction];
        }

        return [];

    }

    /**
     * @return array
     * @throws ReflectionException
     */
    protected function getUserFilters()
    {
        // Prioritize GET-parameter
        //        if ($parameter = request()->query($parameterName)) {
        //            return $this->parseUserOrderGet($parameter);
        //        }

        $cookie = json_decode(Cookie::get('resources'), true);
        if (isset($cookie[$this->resource->getName()])) {
            return $this->parseUserFilterCookie($cookie[$this->resource->getName()]);
        }

        return [];
    }

    /**
     * @param array $cookie
     *
     * @return array
     */
    protected function parseUserFilterCookie(array $cookie): array
    {
        $filters = [];

        if (isset($cookie['filters'])) {
            foreach ($cookie['filters'] as $field => $value) {
                if (!empty($value)) {
                    $filters[$field] = $value;
                }
            }
        }

        return $filters;
    }
}
