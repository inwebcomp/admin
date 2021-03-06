<?php

namespace InWeb\Admin\App;

use App\Admin\Resources\Page;
use App\Admin\Resources\Product;
use BadMethodCallException;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\Str;
use InWeb\Admin\App\Events\ServingAdmin;
use InWeb\Admin\App\Http\Requests\AdminRequest;
use InWeb\Admin\App\Resources\Resource;
use Symfony\Component\Finder\Finder;

class Admin
{
    use AuthorizesRequests;

    /**
     * The registered dashboard names.
     *
     * @var array
     */
    public static $dashboards = [];

    /**
     * The registered cards for the default dashboard.
     *
     * @var array
     */
    public static $defaultDashboardCards = [];

    /**
     * The registered resource names.
     *
     * @var Resource[]
     */
    public static $resources = [];
    /**
     * An index of resource names keyed by the model name.
     *
     * @var array
     */
    public static $resourcesByModel = [];
    /**
     * The registered groups.
     *
     * @var array
     */
    public static $groups = [];
    /**
     * @var bool
     */
    public static $groupedMenu = false;
    /**
     * @var array
     */
    public static $menu = null;

    /**
     * All of the registered Admin tools.
     *
     * @var array
     */
    public static $tools = [];

    /**
     * All of the registered Admin cards.
     *
     * @var array
     */
    public static $cards = [];

    /**
     * All of the registered Admin tool scripts.
     *
     * @var array
     */
    public static $scripts = [];
    /**
     * All of the registered Admin tool CSS.
     *
     * @var array
     */
    public static $styles = [];
    /**
     * The variables that should be made available on the Admin JavaScript object.
     *
     * @var array
     */
    public static $jsonVariables = [];
    /**
     * Admin default locale
     *
     * @var string
     */
    public static $defaultLocale;

    /* The callable that resolves the user's timezone.
     *
     * @var callable
     */
    public static $userTimezoneCallback;

    /**
     * The debounce amount to use when using global search.
     *
     * @var float
     */
    public static $debounce = 0.5;

    /**
     * Get the URI path prefix utilized by Admin.
     *
     * @return string
     */
    public static function path()
    {
        return config('admin.path', 'admin');
    }

    /**
     * Register the Admin routes.
     */
    public static function routes()
    {
        return new PendingRouteRegistration();
    }

    /**
     * Register an event listener for the Admin "serving" event.
     *
     * @param \Closure|string $callback
     * @return void
     */
    public static function serving($callback)
    {
        Event::listen(ServingAdmin::class, $callback);
    }

    /**
     * Get meta data information about all resources for client side consumption.
     *
     * @param \Illuminate\Http\Request $request
     * @return array
     */
    public static function resourceInformation(Request $request)
    {
        return collect(static::$resources)->map(function ($resource) use ($request) {
            return $resource::info();
        })->values()->all();
    }

    /**
     * Get the resources available for the given request.
     *
     * @param \Illuminate\Http\Request $request
     * @return array
     */
    public static function availableResources(Request $request)
    {
        return collect(static::$resources)->filter(function ($resource) use ($request) {
            return $resource::authorizedToViewAny($request) &&
                   $resource::availableForNavigation($request);
        })->all();
    }

    /**
     * Get the resources available for the given request.
     *
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Support\Collection
     */
    public static function globallySearchableResources(Request $request)
    {
        return collect(static::availableResources($request))
            ->filter(function ($resource) {
                return $resource::$globallySearchable;
            });
    }

    /**
     * Register the given resources.
     *
     * @param array $resources
     * @return static
     */
    public static function resources(array $resources)
    {
        static::$resources = array_merge(static::$resources, $resources);

        return new static;
    }

    /**
     * Register the given group.
     *
     * @param string $key
     * @param string|array $info
     * @param string|null $icon
     * @return static
     */
    public static function group($key, $info, $icon = null)
    {
        if (! is_array($info)) {
            $info = [
                'label' => $info,
                'icon'  => $icon,
            ];
        }

        static::$groups[$key] = $info;

        return new static;
    }

    /**
     * Return group info by key.
     *
     * @param string $key
     * @return static
     */
    public static function groupInfo($key)
    {
        return static::$groups[$key] ?? null;
    }

    /**
     * Get the available resource groups for the given request.
     *
     * @param Request $request
     * @return array
     */
    public static function groups(Request $request)
    {
        return collect(static::availableResources($request))
            ->map(function ($item, $key) {
                return $item::group();
            })->unique()->values();
    }

    /**
     * Get the grouped resources available for the given request.
     *
     * @param Request $request
     * @return array
     */
    public static function groupedResources(Request $request)
    {
        return collect(static::availableResources($request))
            ->groupBy(function ($item, $key) {
                return $item::group();
            })->all();
    }

    /**
     * Get the grouped resources available for the given request.
     *
     * @param Request $request
     * @return array
     */
    public static function groupedResourcesAndTools(Request $request)
    {
        if (Admin::$menu) {
            $menu = Admin::$menu;

            foreach ($menu as $key => $resources) {
                $menu[$key] = collect($resources)->filter(function ($resource) use ($request) {
                    $resource = new $resource;
                    return $resource->authorize($request) &&
                           $resource::availableForNavigation($request);
                })->all();
            }

            return $menu;
        }

        return collect(array_merge(
            static::availableResources($request),
            Admin::availableToolsForNavigation($request)
        ))->groupBy(function ($item, $key) {
            return $item::group();
        })->all();
    }

    /**
     * Register all of the resource classes in the given directory.
     *
     * @param string $directory
     * @return void
     * @throws \ReflectionException
     */
    public static function resourcesIn($directory)
    {
        $namespace = app()->getNamespace();

        $resources = [];

        foreach ((new Finder)->in($directory)->files() as $resource) {
            $resource = $namespace . str_replace(
                    ['/', '.php'],
                    ['\\', ''],
                    Str::after($resource->getPathname(), app_path() . DIRECTORY_SEPARATOR)
                );

            if (is_subclass_of($resource, Resource::class) &&
                ! (new \ReflectionClass($resource))->isAbstract()) {
                $resources[] = $resource;
            }
        }

        static::resources(
            collect($resources)->sort()->all()
        );
    }

    /**
     * Get the resource class name for a given key.
     *
     * @param string $key
     * @return Resource
     */
    public static function resourceForKey($key)
    {
        return collect(static::$resources)->first(function ($value) use ($key) {
            return $value::uriKey() === $key;
        });
    }

    /**
     * Get a new resource instance with the given model instance.
     *
     * @param \Illuminate\Database\Eloquent\Model $model
     * @return Resource
     */
    public static function newResourceFromModel($model)
    {
        $resource = static::resourceForModel($model);

        return new $resource($model);
    }

    /**
     * Get the resource class name for a given model class.
     *
     * @param object|string $class
     * @return string
     */
    public static function resourceForModel($class)
    {
        if (is_object($class)) {
            $class = get_class($class);
        }

        if (isset(static::$resourcesByModel[$class])) {
            return static::$resourcesByModel[$class];
        }

        $resource = collect(static::$resources)->first(function ($value) use ($class) {
            return $value::$model === $class;
        });

        return static::$resourcesByModel[$class] = $resource;
    }

    /**
     * Get a resource instance for a given key.
     *
     * @param string $key
     * @return Resource|null
     */
    public static function resourceInstanceForKey($key)
    {
        if ($resource = static::resourceForKey($key)) {
            return new $resource($resource::newModel());
        }
    }

    /**
     * Get a fresh model instance for the resource with the given key.
     *
     * @param string $key
     * @return \Illuminate\Database\Eloquent\Model
     */
    public static function modelInstanceForKey($key)
    {
        $resource = static::resourceForKey($key);

        return $resource ? $resource::newModel() : null;
    }


    /**
     * Register new tools with Admin.
     *
     * @param array $tools
     * @return static
     */
    public static function tools(array $tools)
    {
        static::$tools = array_merge(
            static::$tools,
            $tools
        );

        return new static;
    }

    /**
     * Get the tools registered with Admin.
     *
     * @return array
     */
    public static function registeredTools()
    {
        return static::$tools;
    }

    /**
     * Get the tool class name for a given key.
     *
     * @param string $key
     * @return Resource
     */
    public static function toolForKey($key)
    {
        return collect(static::$tools)->first(function ($value) use ($key) {
            return $value::uriKey() === $key;
        });
    }

    /**
     * Boot the available Admin tools.
     *
     * @param \Illuminate\Http\Request $request
     * @return void
     */
    public static function bootTools(Request $request)
    {
        collect(static::availableTools($request))->each->boot();
    }

    /**
     * Get the tools registered with Admin.
     *
     * @param \Illuminate\Http\Request $request
     * @return array
     */
    public static function availableTools(Request $request)
    {
        return collect(static::$tools)->filter->authorize($request)->all();
    }

    /**
     * Get the tools registered with Admin.
     *
     * @param \Illuminate\Http\Request $request
     * @return array
     */
    public static function availableToolsForNavigation(Request $request)
    {
        return collect(static::$tools)->filter(function ($tool) use ($request) {
            return $tool->authorize($request) &&
                   $tool::availableForNavigation($request);
        })->all();
    }

    /**
     * Set the callable that resolves the user's preferred timezone.
     *
     * @param callable $userTimezoneCallback
     * @return static
     */
    public static function userTimezone($userTimezoneCallback)
    {
        static::$userTimezoneCallback = $userTimezoneCallback;

        return new static;
    }

    /**
     * Resolve the user's preferred timezone.
     *
     * @param \Illuminate\Http\Request $request
     * @return string|null
     */
    public static function resolveUserTimezone(Request $request)
    {
        if (static::$userTimezoneCallback) {
            return call_user_func(static::$userTimezoneCallback, $request);
        }
    }

    /**
     * Register new dashboard cards with Admin.
     *
     * @param array $cards
     * @return static
     */
    public static function cards(array $cards)
    {
        static::$cards = array_merge(
            static::$cards,
            $cards
        );

        return new static;
    }

    /**
     * Get the cards registered with Admin.
     *
     * @return array
     */
    public static function registeredCards()
    {
        return static::$cards;
    }

    /**
     * Get the cards registered with Admin.
     *
     * @param \Illuminate\Http\Request $request
     * @return array
     */
    public static function availableCards(Request $request)
    {
        return collect(static::$cards)->filter->authorize($request)->all();
    }

    /**
     * Copy the cards to cards to the default dashboard.
     *
     * @return static
     */
    public static function copyDefaultDashboardCards()
    {
        static::$defaultDashboardCards = static::$cards;

        return new static;
    }

    /**
     * Get the dashboards registered with Admin.
     *
     * @param \Illuminate\Http\Request $request
     * @return array
     */
    public static function availableDashboards(Request $request)
    {
        return collect(static::$dashboards)->filter->authorize($request)->all();
    }

    /**
     * Register the dashboards.
     *
     * @param array $dashboards
     * @return static
     */
    public static function dashboards(array $dashboards)
    {
        static::$dashboards = array_merge(static::$dashboards, $dashboards);

        return new static;
    }

    /**
     * Get the available dashboard cards for the given request.
     *
     * @param AdminRequest $request
     * @return Collection
     */
    public static function allAvailableDashboardCards(AdminRequest $request)
    {
        return collect(static::$dashboards)
            ->filter
            ->authorize($request)
            ->flatMap(function ($dashboard) {
                return $dashboard->cards();
            })->merge(static::$cards)
            ->unique()
            ->filter
            ->authorize($request)
            ->values();
    }

    /**
     * Get the available dashboard for the given request.
     *
     * @param string $dashboard
     * @param AdminRequest $request
     * @return Collection
     */
    public static function dashboardForKey($dashboard, AdminRequest $request)
    {
        return collect(static::$dashboards)
            ->filter
            ->authorize($request)
            ->first(function ($dash) use ($dashboard) {
                return $dash::uriKey() === $dashboard;
            });
    }

    /**
     * Get the available dashboard cards for the given request.
     *
     * @param string $dashboard
     * @param AdminRequest $request
     * @return Collection
     */
    public static function availableDashboardCardsForDashboard($dashboard, AdminRequest $request)
    {
        return collect(static::$dashboards)->filter->authorize($request)->filter(function ($dash) use ($dashboard) {
            return $dash->uriKey() === $dashboard;
        })->flatMap(function ($dashboard) {
            return $dashboard->cards();
        })->filter->authorize($request)->values();
    }

    /**
     * Get all of the additional scripts that should be registered.
     *
     * @return array
     */
    public static function allScripts()
    {
        return static::$scripts;
    }

    /**
     * Get all of the available scripts that should be registered.
     *
     * @param \Illuminate\Http\Request $request
     * @return array
     */
    public static function availableScripts(Request $request)
    {
        return static::$scripts;
    }

    /**
     * Get all of the additional stylesheets that should be registered.
     *
     * @return array
     */
    public static function allStyles()
    {
        return static::$styles;
    }

    /**
     * Get all of the available stylesheets that should be registered.
     *
     * @param \Illuminate\Http\Request $request
     * @return array
     */
    public static function availableStyles(Request $request)
    {
        return static::$styles;
    }

    /**
     * Register the given script file with Admin.
     *
     * @param string $name
     * @param string $path
     * @return static
     */
    public static function script($name, $path)
    {
        static::$scripts[$name] = $path;

        return new static;
    }

    /**
     * Register the given remote script file with Admin.
     *
     * @param string $path
     * @return static
     */
    public static function remoteScript($path)
    {
        static::$scripts[md5($path)] = $path;

        return new static;
    }

    /**
     * Register the given CSS file with Admin.
     *
     * @param string $name
     * @param string $path
     * @return static
     */
    public static function style($name, $path)
    {
        static::$styles[$name] = $path;

        return new static;
    }

    /**
     * Get the JSON variables that should be provided to the global Admin JavaScript object.
     *
     * @param \Illuminate\Http\Request $request
     * @return array
     */
    public static function jsonVariables(Request $request)
    {
        return collect(static::$jsonVariables)->map(function ($variable) use ($request) {
            return is_callable($variable) ? $variable($request) : $variable;
        })->all();
    }

    /**
     * Provide additional variables to the global Admin JavaScript object.
     *
     * @param array $variables
     * @return static
     */
    public static function provideToScript(array $variables)
    {
        if (empty(static::$jsonVariables)) {
            static::$jsonVariables = [
                'baseUrl'     => static::path(),
                'storagePath' => url('storage'),
                'language'    => \App::getLocale(),
                'languages'   => config('inweb.languages'),
                'sitename'    => config('app.name'),
                'user'        => \Auth::guard('admin')->user(),
                'groupedMenu' => static::$groupedMenu,
                'debounce'    => static::$debounce * 1000,
            ];
        }

        static::$jsonVariables = array_merge(static::$jsonVariables, $variables);

        return new static;
    }

    /**
     * Humanize the given value into a proper name.
     *
     * @param string $value
     * @return string
     */
    public static function humanize($value)
    {
        if (is_object($value)) {
            return static::humanize(class_basename(get_class($value)));
        }

        return Str::title(Str::snake($value, ' '));
    }

    public static function setLocale($locale)
    {
        \App::setLocale($locale);
        \Carbon\Carbon::setLocale($locale);
        setlocale(LC_ALL, $locale . '_' . strtoupper($locale) . '.UTF-8', $locale);
    }

    public static function setDefaultLocale($locale)
    {
        self::$defaultLocale = $locale;
    }

    /**
     * Dynamically proxy static method calls.
     *
     * @param string $method
     * @param array $parameters
     * @return void
     */
    public static function __callStatic($method, $parameters)
    {
        if (! property_exists(get_called_class(), $method)) {
            throw new BadMethodCallException("Method {$method} does not exist.");
        }

        return static::${$method};
    }

    public static function groupedMenu()
    {
        self::$groupedMenu = true;
    }

    public static function setMenu($menu)
    {
        self::$menu = $menu;
    }

    /**
     * Return the debounce amount to use when using global search.
     *
     * @var int
     */
    public static function globalSearchDebounce($debounce)
    {
        static::$debounce = $debounce;

        return new static;
    }
}


