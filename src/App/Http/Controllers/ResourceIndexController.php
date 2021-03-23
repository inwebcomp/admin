<?php

namespace InWeb\Admin\App\Http\Controllers;

use App\Admin\Resources\ActivityMonitor;
use App\Admin\Resources\Product;
use Illuminate\Pagination\Paginator;
use Illuminate\Support\Collection;
use InWeb\Admin\App\Contracts\Nested;
use InWeb\Admin\App\Http\Requests\ResourceIndexRequest;
use InWeb\Admin\App\Resources\Resource;
use InWeb\Base\Entity;
use InWeb\Admin\App\Parameters;

class ResourceIndexController extends Controller
{
    public $perPage = 20;

    /**
     * @param ResourceIndexRequest $request
     * @return \Illuminate\Http\JsonResponse
     * @throws \Throwable
     */
    public function handle(ResourceIndexRequest $request)
    {
        $paginator = $this->paginator(
            $request, $resource = $request->resource()
        );

        /** @var Resource $resourceObject */
        $resourceObject = new $resource;
        $resourceObject->authorizeToViewAny($request);

        /** @var Collection $resources */
        $resources = $paginator->getCollection();

        if ($resource::$groupBy) {
            $resources = $resources->groupBy($resource::$groupBy)
                                   ->map(function ($group, $value) use ($resourceObject, $request, $resource) {
                                       $resources = collect($group)->mapInto($resource);

                                       return [
                                           'groupInfo' => $resources->first()->groupInfo($request, $value, $resources),
                                           'resources' => $resources->map->serializeForIndex($request)
                                       ];
                                   })
                                   ->values();
        } else {
            $resources = $resources->mapInto($resource)->map->serializeForIndex($request);
        }

        return response()->json(array_merge([
            'info'               => $resource::info(),
            'resources'          => $resources,
            'pagination'         => $this->getPagination($paginator),
            'authorizedToCreate' => $resource::authorizedToCreate($request),
            'authorizedToDelete' => $resourceObject->authorizedToDelete($request),
        ], (new $resource instanceof Nested) ? [
            'breadcrumbs' => $this->getBreadcrumbs($request),
        ] : []));
    }

    /**
     * Get the paginator instance for the index request.
     *
     * @param ResourceIndexRequest $request
     * @param string $resource
     * @return \Illuminate\Contracts\Pagination\LengthAwarePaginator
     */
    protected function paginator(ResourceIndexRequest $request, $resource)
    {
        $res = new $resource;
        $query = $request->toQuery();
        /** @var Entity $model */
        $model = $res->model();

        // @todo Refactor this
        if ($res instanceof Nested) {
            $wasParent = Parameters::get($resource, 'parent');
            $parent = Parameters::remember($request, $resource, 'parent');

            if ($wasParent != $parent) {
                Parameters::remove($resource, 'page');
            }

            if ($parent and $item = $res->nestedRelationResource()->model()->withoutGlobalScopes()->find($parent)) {
                if ($res instanceof Product) { // @todo
                    $query->whereIn('category_id', array_merge(
                        $item->getDescendants([$item->getKeyName()])->pluck($item->getKeyName())->toArray(),
                        [$item->id]
                    ));
                } else {
                    $query->where($res->nestedRelationResourceField(), $item->id);
                }
            } else {
                if ($model instanceof \InWeb\Base\Contracts\Nested) {
                    $query->whereIsRoot();
                }
            }
        }

        $query->withoutGlobalScopes();

        if ($model->translatable())
            $query->withTranslation();

        $page = Parameters::remember($request, $resource, 'page');

        if ($page) {
            Paginator::currentPageResolver(function () use ($page) {
                return $page;
            });
        }

        if ($query->getQuery()->limit) {
            $perPage = $query->getQuery()->limit;
            $query->getQuery()->limit = null;
        } else {
            $perPage = $this->perPage;
        }

        return $query->paginate($perPage);
    }

    private function getPagination(\Illuminate\Pagination\LengthAwarePaginator $paginator)
    {
        return [
            'currentPage' => $paginator->currentPage(),
            'perPage'     => $paginator->perPage(),
            'total'       => $paginator->total(),
            'lastPage'    => $paginator->lastPage(),
        ];
    }

    private function getBreadcrumbs(ResourceIndexRequest $request)
    {
        $item = null;
        $model = $request->newResource()->nestedRelationResource()->model();

        $parent = Parameters::get($request->resource(), 'parent');

        $item = $model::withoutGlobalScopes()->find($parent);

        return $request->newResource()->nestedRelationResource()->breadcrumbs($request, $item);
    }
}
