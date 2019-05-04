<?php

namespace InWeb\Admin\App\Http\Controllers;

use Illuminate\Pagination\Paginator;
use InWeb\Admin\App\Contracts\Nested;
use InWeb\Admin\App\Http\Requests\ResourceIndexRequest;
use InWeb\Admin\App\Models\Entity;
use InWeb\Admin\App\Parameters;

class ResourceIndexController extends Controller
{
    public $perPage = 20;

    public function handle(ResourceIndexRequest $request)
    {
        $paginator = $this->paginator(
            $request, $resource = $request->resource()
        );

        return response()->json(array_merge([
            'info' => $resource::info(),
            'resources' => $paginator->getCollection()->mapInto($resource)->map->serializeForIndex($request),
            'pagination' => $this->getPagination($paginator),
        ], (new $resource instanceof Nested) ? [
            'breadcrumbs' => $this->getBreadcrumbs($request),
        ] : []));
    }

    /**
     * Get the paginator instance for the index request.
     *
     * @param ResourceIndexRequest $request
     * @param string                     $resource
     * @return \Illuminate\Pagination\LengthAwarePaginator
     */
    protected function paginator(ResourceIndexRequest $request, $resource)
    {
        $res = new $resource;
        $query = $res->query();
        /** @var Entity $model */
        $model = $res->model();

        // @todo Refactor this
        if ($res instanceof Nested) {
            $parent = Parameters::remember($request, $resource, 'parent');

            if ($item = $model::find($parent)) {
                $query->where('parent_id', $item->id);
            } else {
                $query->whereIsRoot();
            }
        }

        if ($model->translatable())
            $query->withTranslation();

        if ($model->positionable())
            $query->ordered();

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
            'total' => $paginator->total(),
            'lastPage' => $paginator->lastPage(),
        ];
    }

    private function getBreadcrumbs(ResourceIndexRequest $request)
    {
        $item = null;
        $model = $request->newResource()->model();

        $parent = Parameters::get($request->resource(), 'parent');

        $item = $model::find($parent);

        return $request->newResource()->breadcrumbs($item);
    }
}
