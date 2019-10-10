<?php

namespace InWeb\Admin\App\Http\Controllers;

use App\Admin\Resources\Product;
use Illuminate\Pagination\Paginator;
use InWeb\Admin\App\Contracts\Nested;
use InWeb\Admin\App\Http\Requests\ResourceIndexRequest;
use InWeb\Base\Entity;
use InWeb\Admin\App\Parameters;
use Session;

class ResourceIndexController extends Controller
{
    public $perPage = 20;

    public function handle(ResourceIndexRequest $request)
    {
        $paginator = $this->paginator(
            $request, $resource = $request->resource()
        );

        $resourceObject = new $resource;
        $resourceObject->authorizeToViewAny($request);

        return response()->json(array_merge([
            'info'               => $resource::info(),
            'resources'          => $paginator->getCollection()->mapInto($resource)->map->serializeForIndex($request),
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

        return $query->paginate($this->perPage);
    }

    private function getPagination(\Illuminate\Pagination\LengthAwarePaginator $paginator)
    {
        return [
            'currentPage' => $paginator->currentPage(),
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

        return $request->newResource()->nestedRelationResource()->breadcrumbs($item);
    }
}
