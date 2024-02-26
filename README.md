"Curious minds often converge in the same ideas" - somebody

This project became obsolete with the release of Laravel Nova. I revisited the code in 2020 and 2022 in order to finish the original idea.

You can define "resources" (see Laravel Nova) and register them via ServiceProviders. The system will automatically create a CRUD for each resource (routes, tables and forms)

At glance a Resource may look similar to this:

```php
/**
 * Class User
 */
class User extends AbstractResource
{
    use ObservableResourceTrait;

    /**
     * The unique name of this resource
     * This will be used as argument for the resource routes
     *
     * It can be left null, in this case the class basename
     * will be used.
     *
     * @var string
     */
    protected static $name;

    /**
     * Define a label for forms and single views
     *
     * @var string
     */
    protected static $label = 'User';

    /**
     * Define a title for listing and tables
     *
     * @var string
     */
    protected static $title = 'Users';

    /**
     * Define the field that should be used when labeling a record
     *
     * @var string
     */
    protected $labelField = 'name';

    /**
     * The Eloquent Model this resource will use
     *
     * @return string
     */
    public static function model(): string
    {
        return Model::class;
    }

    /**
     * Define initially the array of Field objects
     *
     * @return array
     */
    public function fields(): array
    {
        return array_merge([
            Primary::create()
                ->setName('id')
                ->setLabel('ID')
                ->isFilterable(true)
                ->isSortable(true),

            Text::create()
                ->setName('name')
                ->setLabel('Name')
                ->isFilterable(true)
                ->isSortable(true),

            Text::create()
                ->setName('email')
                ->setLabel('Email')
                ->isFilterable(true)
                ->isSortable(true)
                ->setFormat(function($value) {
                    return '<a href="mailto:'.$value.'">'.$value.'</a>';
                }),

            Text::create()
                ->setName('email_verified_at')
                ->setLabel('Email verified at')
                ->isFilterable(true)
                ->isSortable(true)
                ->addNotInContext('table')
                ->addNotInContext('form'),

            Password::create()
                ->setName('password')
                ->setLabel('Password')
                ->setInputType('password-strength')
                ->addNotInContext('table')
                ->addNotInContext('show')
                ->setRules('nullable|same:password_confirm|min:6|regex:/^.*(?=.{1,})(?=.*[a-zA-Z])(?=.*[0-9])(?=.*[\d\X])(?=.*[!$#%]).*$/')
                ->setDescription(__('The password must contain at least 6 characters, of which at least 1 is a uppercase letter, 1 is a lowercase letter, 1 is a number and 1 is a non-alphanumeric character (for example !, $, #, or %)')),

            PasswordConfirm::create()
                ->setName('password_confirm')
                ->setLabel('Confirm password')
                ->isVirtual(true)
                ->addNotInContext('table')
                ->addNotInContext('show')
                ->setRules('required_with:password|same:password'),

            Text::create()
                ->setName('api_token')
                ->setLabel('API-Token')
                ->isFilterable(true)
                ->isSortable(true)
                ->addNotInContext('form'),

            Text::create()
                ->setName('remember_token')
                ->setLabel('Remember token')
                ->addNotInContext('table')
                ->addNotInContext('show')
                ->addNotInContext('form'),

            BelongsToMany::create()
                ->setName('role_id')
                ->setLabel('Roles')
                ->setInputType('select2-multiple')
                ->setForeignResource(Role::class)
                ->setMethod('roles'),

        ], $this->getMetaFields());

    }
```
