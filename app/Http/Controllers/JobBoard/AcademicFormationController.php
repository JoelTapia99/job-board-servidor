<?php

namespace App\Http\Controllers\JobBoard;

use http\Exception;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Database\QueryException;
use Illuminate\Http\Request;
use App\Models\Jobboard\Professional;
use App\Models\JobBoard\AcademicFormation;
use Illuminate\Routing\Controller;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;


class AcademicFormationController extends Controller
{
    function index(Request $request)
    {
//        $academicFormation = Professional::with(['academicFormations' => function ($query) {
//            $query->with(['category' => function ($query) {
//                $query->where('state_id', '1');
//            }]);
//
//        }])->get();


        $academicFormation = Professional::with(['academicFormations' => function ($query) {
            $query->where('state_id', 1);
            }])-where('id', $request->user_id)
                ->where('state_id',1)->get();

        return $academicFormation;
//                ->with(['eventType' => function ($query) {
//                    $query->where('state_id', 1);
//                }])
//                ->with(['certificationType' => function ($query) {
//                    $query->where('state_id', 1);
//                }]);

//        }])->where('id', $request->user_id)
//            ->orderby($request->field, $request->order)
//            ->paginate($request->limit);
//        return response()->json([
//            'pagination' => [
//                'total' => $courses->total(),
//                'current_page' => $courses->currentPage(),
//                'per_page' => $courses->perPage(),
//                'last_page' => $courses->lastPage(),
//                'from' => $courses->firstItem(),
//                'to' => $courses->lastItem()
//            ], 'courses' => $courses], 200);
//
//
//        return response()->json([
//            'data' => ['academic_formations' => $academicFormation]], 200);
    }

    function show($id)
    {
        try {
            $academicFormation = AcademicFormation::findOrFail($id);
            return response()->json(['data' => ['academic_formation' => $academicFormation]], 200);
        } catch (ModelNotFoundException $e) {
            return response()->json($e, 405);
        } catch (NotFoundHttpException  $e) {
            return response()->json($e, 405);
        } catch (QueryException $e) {
            return response()->json($e, 400);
        } catch (Exception $e) {
            return response()->json($e, 500);
        } catch (Error $e) {
            return response()->json($e, 500);
        }
    }


    function store(Request $request)
    {
        try {
            $data = $request->json()->all();
            $dataUser = $data['user'];
            $dataAcademicFormation = $data['academic_formation'];
            $professional = Professional::where('user_id', $dataUser['id'])->first();
            if ($professional) {
                $academicFormation = new AcademicFormation();
                $academicFormation->registration_date = $dataAcademicFormation ['registration_date'];
                $academicFormation->senescyt_code = strtoupper($dataAcademicFormation ['senescyt_code']);
                $academicFormation->has_titling = $dataAcademicFormation ['has_titling'];

                $category = Category::findOrFail($dataAcademicFormation['category']['id']);
                $state = State::where('code', '1')->first();

                $academicFormation->category()->associate($category);
                $academicFormation->state()->associate($state);
                $dataAcademicFormation->save();

                return response()->json($dataAcademicFormation, 201);
            } else {
                return response()->json(null, 404);
            }
        } catch (ModelNotFoundException $e) {
            return response()->json($e, 405);
        } catch (NotFoundHttpException  $e) {
            return response()->json($e, 405);
        } catch (QueryException $e) {
            return response()->json($e, 400);
        } catch (Exception $e) {
            return response()->json($e, 500);
        } catch (Error $e) {
            return response()->json($e, 500);
        }
    }

    function update(Request $request)
    {
        try {
            $data = $request->json()->all();
            $dataUser = $data['user'];
            $dataAcademicFormation = $data['academic_formation'];
            $academicFormation = AcademicFormation::findOrFail($dataAcademicFormation ['id']);


            if ($academicFormation) {

                $academicFormation = new AcademicFormation();

                $academicFormation->registration_date = $dataAcademicFormation ['registration_date'];
                $academicFormation->senescyt_code = strtoupper($dataAcademicFormation ['senescyt_code']);
                $academicFormation->has_titling = $dataAcademicFormation ['has_titling'];

                $category = Category::findOrFail($dataAcademicFormation['category']['id']);

                $academicFormation->category()->associate($category);
                $academicFormation->save();

                return response()->json($academicFormation, 201);
            } else {
                return response()->json(null, 404);
            }
        } catch (ModelNotFoundException $e) {
            return response()->json($e, 405);
        } catch (NotFoundHttpException  $e) {
            return response()->json($e, 405);
        } catch (QueryException $e) {
            return response()->json($e, 400);
        } catch (Exception $e) {
            return response()->json($e, 500);
        } catch (Error $e) {
            return response()->json($e, 500);
        }

    }


    function destroy($id)
    {
        $academicFormation = AcademicFormation:: findOrFail($id);
        $state = State::where('code', '3')->first();
        $academicFormation->state()->associate($state);
        $academicFormation->save();
        $academicFormations = AcademicFormation:: with(['state' => function ($query) {
            $query->where('code', '1');
        }])
            ->get();
        return response()->json([
            'data' => [
                'type' => 'academicFormations',
                'academicFormation' => $academicFormations
            ]
        ], 200);
    }
}
