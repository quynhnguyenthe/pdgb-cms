<?php

namespace App\Http\Services;

use App\Constants\Consts;
use Illuminate\Support\Facades\DB;
use Symfony\Component\HttpFoundation\Response;

class BaseService
{
    protected $model;

    public function getParams($request)
    {
        $fillable = $this->getFillable();
        return $request->only($fillable);
    }

    public function getFillable()
    {
        return $this->model->getFillable();
    }

    public function create($request)
    {
       try {
            $params = $this->getParams($request);
            return $this->model->create($params);
       } catch (\Exception $exception) {
           return $this->error();
       }

    }

    public function update($id, $request)
    {
        $params = $this->getParams($request);
        $object = $this->model->findOrFail($id);
        foreach ($params as $key => $value) {
            $object->{$key} = $value;
        }
        try {
            $object->save();
            return $object;
        } catch (\Exception $exception) {
            return $this->error();
        }

    }

    public function getTotalRow($query)
    {
        return DB::table(DB::raw("({$query->toSql()}) as count"))
            ->mergeBindings($query->getQuery())
            ->get()
            ->count();
    }

    /**
     * @method formatItems
     * Overwrite this method to modify data
     * @param $items
     * @return mixed*/
    final function formatItems($items)
    {
        return $items;
    }

    public function basePaginate($query, $request, $format = 'formatItems')
    {
        $page = isset($request->page) ? intval($request->page) : Consts::BASE_PAGE;
        $perPage = isset($request->size) ? intval($request->size) : Consts::BASE_PAGE_SIZE;
        $skip = $page === 1 ? 0 : ($perPage * ($page - 1));
        $totalRow = $this->getTotalRow($query);
        $totalPage = ceil($totalRow / $perPage);
        $items = $query->paginate(\App\Constants\Consts::BASE_PAGE_SIZE);
        $items = $this->{$format}($items);
        return [
            'records' => $items,
            'totalRow' => $totalRow,
            'totalPage' => $totalPage,
            'currentPage' => $page,
            'limitFrom' => count($items) ? $skip + 1 : 0,
            'limitTo' => $skip + count($items)
        ];
    }

    public function index($request, $fieldOrderBy = 'created_at', $typeOrderBy = 'DESC')
    {
        $table = $this->model->getTable();

        return $this->basePaginate($this->model->orderBy("{$table}.{$fieldOrderBy}", "{$typeOrderBy}"), $request);
    }

    public function insert($datas)
    {
        return $this->model->insert($datas);
    }

    public function delete($id)
    {
        try {
            $this->model->findOrFail($id)->delete();
        } catch (\Exception $exception) {
            return $this->error();
        }
    }

    protected function success($data = [], $messages = null)
    {
        return [
            'status' => Response::HTTP_OK,
            'messages' => $messages ?? [__('success')],
            'data' => $data
        ];
    }

    protected function error($messages = null, $data = [])
    {
        return [
            'status' => Response::HTTP_INTERNAL_SERVER_ERROR,
            'messages' => $messages ?? __('server_error'),
            'data' => $data
        ];
    }

    protected function notFound($messages = null)
    {
        return [
            'status' => Response::HTTP_NOT_FOUND,
            'messages' => $messages,
        ];
    }

    protected function notAllowed($messages = ['Not Allowed'])
    {
        return [
            'status' => Response::HTTP_FORBIDDEN,
            'messages' => $messages ?? __('Not Found')
        ];
    }

    protected function errorParam($messages = ['Bad Request'])
    {
        return [
            'status' => Response::HTTP_BAD_REQUEST,
            'messages' => $messages
        ];
    }

}
