<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Traits\BaseResponse;
use Illuminate\Http\Request;

class BaseApiController extends Controller
{
    use BaseResponse;
    protected $service;

    public function destroy($id)
    {
        $result = $this->service->destroy($id);
        return $this->baseResponse($result);
    }

    public function getList(Request $request)
    {
        $data = $this->service->getList($request);
        return $this->success($data);
    }

    public function show($id)
    {
        $result = $this->service->show($id);
        return $result;
    }

    public function index(Request $request)
    {
        $result = $this->service->index($request);
        return $this->success($result);
    }
}
