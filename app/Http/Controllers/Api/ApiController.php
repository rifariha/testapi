<?php

namespace App\Http\Controllers\Api;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class ApiController extends Controller
{


    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */

    public function index()
    {
        $users = DB::table('pp_user')->get();
        return response()->json(['status' => true, 'data' => $users,'message' => "OK"],200);
    }

    public function pp_gedung()
    {
        $pp_gedung = DB::table('pp_gedung')->get();
        return response()->json(['status' => true, 'data' => $pp_gedung,'message' => "OK"],200);
    }

    public function pp_laporan_apar()
    {
        $pp_laporan_apar = DB::table('pp_laporan_apar')->get();
        return response()->json(['status' => true, 'data' => $pp_laporan_apar,'message' => "OK"],200);
    }

    public function pp_laporan_apar_detail($id)
    {
        $pp_laporan_apar = DB::table('pp_laporan_apar')->where('qrCode', '=', $id)->first();
        if($pp_laporan_apar){
          return response()->json(['status' => true, 'data' => $pp_laporan_apar,'message' => "OK"],200);
        }else{
          return response()->json(['status' => false, 'data' => null,'message' => "data tidak ditemukan"],200);
        }

    }

    public function ganti_password(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'password_lama'           => 'required',
            'password_baru'        => 'required',
            'id_user'        => 'required',
        ]);

        if ($validator->fails()) {
            $result = $this->validationErrorsToString($validator->errors());
            return response()->json(['status' => false, 'description' => $result, 'data' => null]);
        }else {
          $users = DB::table('pp_user')->where('id', $request->id_user)->first();
          if($users){
            $password = md5($request->password_lama);
            if($users->password == $password){

              $pp_laporan_apar = DB::table('pp_user')->where('id', $request->id_user)
              ->update([
                'password' => md5($request->password_baru)
              ]);

              return response()->json(['status' => true, 'description' => 'Password berhasil diubah', 'data' => $users]);
            }else{
              return response()->json(['status' => false, 'description' => 'Password lama Tidak cocok', 'data' => null]);
            }
          }else{
            return response()->json(['status' => false, 'description' => 'User Tidak Ditemukan', 'data' => null]);
          }
        }


    }




    public function home()
    {

        $APAR_rusak = DB::table('pp_laporan_apar')->where('jenisAPAR', '=', 'PAR')
        ->where(function($query) {
            $query->orWhere('cond_pressure', 'NOT OK')
                  ->orWhere('cond_nozzle', 'NOT OK')
                  ->orWhere('cond_segel', 'NOT OK')
                  ->orWhere('cond_hose', 'NOT OK')
                  ->orWhere('cond_physically', 'NOT OK');
        })
        ->get();

        $APAB_rusak = DB::table('pp_laporan_apar')->where('jenisAPAR', '=', 'PAB')
        ->where(function($query) {
            $query->orWhere('cond_pressure', 'NOT OK')
                  ->orWhere('cond_nozzle', 'NOT OK')
                  ->orWhere('cond_segel', 'NOT OK')
                  ->orWhere('cond_hose', 'NOT OK')
                  ->orWhere('cond_physically', 'NOT OK');
        })
        ->get();

        $Hydrant_Pillar_rusak = DB::table('pp_laporan_apar')->where('jenisAPAR', '=', 'PLR')
        ->where(function($query) {
            $query->orWhere('cond_valve', 'NOT OK')
                  ->orWhere('cond_physically', 'NOT OK');
        })
        ->get();

        $Hydrant_Box_rusak = DB::table('pp_laporan_apar')->where('jenisAPAR', '=', 'HBX')
        ->where(function($query) {
            $query->orWhere('cond_nozzle', 'NOT OK')
                  ->orWhere('cond_valve', 'NOT OK')
                  ->orWhere('cond_hose', 'NOT OK')
                  ->orWhere('cond_physically', 'NOT OK');
        })
        ->get();

        $latest = DB::table('pp_laporan_apar')->limit(10)->orderBy('lastUpdate', 'desc')->get();

        $data =[
          'rusak' => [
            'APAR' => [
              'jumlah' => count($APAR_rusak),
              'data' => $APAR_rusak,
            ],
            'APAB' => [
              'jumlah' => count($APAB_rusak),
              'data' => $APAB_rusak,
            ],
            'Hydrant_Pillar' => [
              'jumlah' => count($Hydrant_Pillar_rusak),
              'data' => $Hydrant_Pillar_rusak,
            ],
            'Hydrant_Box' => [
              'jumlah' => count($Hydrant_Box_rusak),
              'data' => $Hydrant_Box_rusak,
            ]
          ],
          'latest' =>$latest
        ];

        return response()->json(['status' => true, 'data' => $data,'message' => "OK"],200);
    }

    private function validationErrorsToString($errArray) {
        $valArr = array();
        foreach ($errArray->toArray() as $key => $value) {
            $errStr = $value[0];
            array_push($valArr, $errStr);
        }
        if(!empty($valArr)){
            $errStrFinal = implode('<br/>', $valArr);
        }
        return $errStrFinal;
    }

    public function login(Request $request)
    {
      $validator = Validator::make($request->all(), [
          'email'           => 'required|email',
          'password'        => 'required',
      ]);

      if ($validator->fails()) {
          $result = $this->validationErrorsToString($validator->errors());
          return response()->json(['status' => false, 'description' => $result, 'data' => null]);
      }else {
          $users = DB::table('pp_user')->where('email', $request->email)->first();
          if($users){
            $password = md5($request->password);
            if($users->password == $password){
              return response()->json(['status' => true, 'description' => 'Berhasil Login', 'data' => $users]);
            }else{
              return response()->json(['status' => false, 'description' => 'Password Tidak cocok', 'data' => null]);
            }
          }else{
            return response()->json(['status' => false, 'description' => 'Email Tidak Terdaftar', 'data' => null]);
          }
      }
    }

    public function save(Request $request)
    {
      date_default_timezone_set('Asia/Jakarta');
      $pp_laporan_apar = DB::table('pp_laporan_apar')->where('qrCode', '=', $request->qrCode)
      ->update([
        'cond_pressure' => $request->cond_pressure == null?'':$request->cond_pressure,
        'cond_nozzle' => $request->cond_nozzle == null?'':$request->cond_nozzle,
        'cond_segel' => $request->cond_segel == null?'':$request->cond_segel,
        'cond_hose' => $request->cond_hose == null?'':$request->cond_hose,
        'cond_physically' => $request->cond_physically == null?'':$request->cond_physically,
        'cond_valve' => $request->cond_valve == null?'':$request->cond_valve,
        'lastUpdate' => date('Y-m-d H:i:s'),
        'keterangan' => $request->keterangan == null?'':$request->keterangan
      ]);
      return response()->json(['status' => true, 'description' => 'Berhasil disimpan']);
    }

    public function save_sync(Request $request)
    {
      $data = $request->data;
      date_default_timezone_set('Asia/Jakarta');
      foreach ($data as $value) {
        if($value['status'] == "Pending"){
        $pp_laporan_apar_chek = DB::table('pp_laporan_apar')->where('qrCode', '=', $value['qrCode'])->first();
        if($pp_laporan_apar_chek){
            $pp_laporan_apar = DB::table('pp_laporan_apar')->where('qrCode', '=', $value['qrCode'])
            ->update([
              'cond_pressure' => $value['cond_pressure'] == null?'':$value['cond_pressure'],
              'cond_nozzle' => $value['cond_nozzle'] == null?'':$value['cond_nozzle'],
              'cond_segel' => $value['cond_segel'] == null?'':$value['cond_segel'],
              'cond_hose' => $value['cond_hose'] == null?'':$value['cond_hose'],
              'cond_physically' => $value['cond_physically'] == null?'':$value['cond_physically'],
              'cond_valve' => $value['cond_valve'] == null?'':$value['cond_valve'],
              'lastUpdate' => $value['lastUpdate'],
              'keterangan' => $value['keterangan'] == null?'':$value['keterangan']
            ]);
          }
        }
      }

      return response()->json(['status' => true, 'description' => 'Berhasil disimpan']);
    }

    public function laporan(Request $request)
    {
      // dd('asas');
       if($request->type == 'PAR'){
         $data = DB::table('pp_laporan_apar')->where('jenisAPAR', '=', 'PAR')
         ->where(function($query) {
             $query->orWhere('cond_pressure', 'NOT OK')
                   ->orWhere('cond_nozzle', 'NOT OK')
                   ->orWhere('cond_segel', 'NOT OK')
                   ->orWhere('cond_hose', 'NOT OK')
                   ->orWhere('cond_physically', 'NOT OK');
         })
         ->get();
       }elseif ($request->type == 'PAB') {
         $data = DB::table('pp_laporan_apar')->where('jenisAPAR', '=', 'PAB')
         ->where(function($query) {
             $query->orWhere('cond_pressure', 'NOT OK')
                   ->orWhere('cond_nozzle', 'NOT OK')
                   ->orWhere('cond_segel', 'NOT OK')
                   ->orWhere('cond_hose', 'NOT OK')
                   ->orWhere('cond_physically', 'NOT OK');
         })
         ->get();
       }elseif ($request->type == 'PLR') {
         $data = DB::table('pp_laporan_apar')->where('jenisAPAR', '=', 'PLR')
         ->where(function($query) {
             $query->orWhere('cond_valve', 'NOT OK')
                   ->orWhere('cond_physically', 'NOT OK');
         })
         ->get();
       }elseif ($request->type == 'HBX') {
         $data = DB::table('pp_laporan_apar')->where('jenisAPAR', '=', 'HBX')
         ->where(function($query) {
             $query->orWhere('cond_nozzle', 'NOT OK')
                   ->orWhere('cond_valve', 'NOT OK')
                   ->orWhere('cond_hose', 'NOT OK')
                   ->orWhere('cond_physically', 'NOT OK');
         })
         ->get();
       }else{
        $data = DB::table('pp_laporan_apar')
        ->where(function($query) {
            $query->orWhere('cond_pressure', 'NOT OK')
                  ->orWhere('cond_nozzle', 'NOT OK')
                  ->orWhere('cond_valve', 'NOT OK')
                  ->orWhere('cond_segel', 'NOT OK')
                  ->orWhere('cond_hose', 'NOT OK')
                  ->orWhere('cond_physically', 'NOT OK');
        })
        ->get();
      }
       return response()->json(['status' => true, 'description' => 'Berhasil menampilkan data', 'data' => $data]);
    }

}
