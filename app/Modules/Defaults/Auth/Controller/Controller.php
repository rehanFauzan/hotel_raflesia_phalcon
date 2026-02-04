<?php

declare(strict_types=1);

namespace App\Modules\Defaults\Auth\Controller;

use App\Modules\Defaults\Auth\Model\MainModel;
use App\Modules\Defaults\Auth\Model\PeriodeAktifModel;
use Core\Facades\Session;
use Phalcon\Escaper;
use Phalcon\Http\Request;
use Core\Facades\Response;
use Phalcon\Mvc\Dispatcher;
use Core\Facades\Security;
use Phalcon\Mvc\Controller as BaseController;
use App\Modules\Defaults\Auth\Model\UsersModel;
use Gregwar\Captcha\CaptchaBuilder;
use Gregwar\Captcha\PhraseBuilder;
use App\Libraries\DatabaseLibNew\SPHelper;

/**
 * @routeGroup("/auth")
 */
class Controller extends BaseController
{

    /**
     * @route(["GET", "POST"], "/")
     */
    public function indexAction()
    {
        return $this->dispatcher->forward([
            'controller' => 'Defaults\\Auth\\Controller',
            'action'     => $this->request->isPost() == true ? 'doLogin' : 'login',
        ]);
        // $this->view->setMainView('Defaults/Auth/index');
    }

    /**
     * @routeGet("/login")
     */
    public function loginAction()
    {

        // Ini Jika Development
        // if (isset($_SERVER['ENV']) && !empty($_SERVER['ENV'])) {
        //     $dir = strtolower(explode("/", $this->request->getUri())[2]);
        // } else {
        //     // Ini Jika Production
        //     $uriParts = explode("/", $this->request->getUri());
        //     $dir = strtolower(isset($uriParts[0]) && $uriParts[0] !== '' ? $uriParts[0] : (isset($uriParts[1]) ? $uriParts[1] : '1'));
        // 

        $dir = 'aurora';

        $pdam = MainModel::findFirst([
            'conditions' => "direktori = :dir:",
            'bind' => [
                'dir' => $dir
            ]
        ]);

        if (!$pdam) {
            return Response::redirect('/Redirect/lost');
        }

        // $phraseBuilder = new PhraseBuilder(5, '0123456789');

        // $builder = new CaptchaBuilder(null, $phraseBuilder);
        // $builder = CaptchaBuilder::create();
        // $builder->build();

        // $builder->save(BASEPATH . '/public/imagesCaptcha/out.jpg');
        // $builder->isOCRReadable();

        // Session::set('phrase', $builder->getPhrase());


        $this->view->setVar('pdam', $pdam);
        $this->view->setMainView('Defaults/Auth/login');
    }

    // /**
    //  * @routeGet("/getEnvironment")
    //  */
    // public function getEnvironmentAction()
    // {
    //     $getPassword = escape_xss($this->request->get('pass'));
    //     if (isset($getPassword) && !empty($getPassword)) {
    //         if ($getPassword == 'aurora_dignity') {
    //         } else {
    //             return Response::redirect('/Redirect/lost');
    //         }
    //     } else {
    //         return Response::redirect('/Redirect/lost');
    //     }
    // }

    /**
     * @routeGet("/unauthorized")
     */
    public function unauthorizedAction()
    {
        $this->view->setMainView('Errors/unauthorized');
    }


    /**
     * @routePost("/login")
     */
    public function doLoginAction()
    {
        if ($this->request->isPost()) {
            $pdamidPost     = $this->request->getPost('pdamid') ?? '';
            $pdamidDirektori = $this->request->getPost('pdamid_dir') ?? '';
            $usernamePost   = escape_xss($this->request->getPost('username'));
            $passwordPost   = escape_xss($this->request->getPost('password'));
            $periodePost = escape_xss($this->request->getPost('periode'));

            // $captcha = escape_xss($this->request->getPost('captcha'));
            $rememberUsername = $this->request->getPost('remember_username');

            if (!isset($pdamidPost) && empty($pdamidPost)) {
                $this->flash->error('Terdeteksi kesalahan alamat login');
                return Response::redirect('/Redirect/lost');
            }

            if (empty($usernamePost)) {
                $this->flash->error('Username harus diisi');
                return Response::redirect('/auth/login');
            }

            if (empty($passwordPost)) {
                $this->flash->error('Password harus diisi');
                return Response::redirect('/auth/login');
            }

            if (empty($periodePost)) {
                $this->flash->error('Periode harus diisi');
                return Response::redirect('/auth/login');
            }

            // if (empty($captcha)) {
            //     $this->flash->error('Captcha harus diisi');
            //     return Response::redirect('/' . $pdamidDirektori . '/auth/login');
            // }

            // if (!$this->security->checkToken()) {
            // $this->flash->error('Terdeteksi peretasan sistem!');
            // return Response::redirect('/auth/login');
            // return Response::redirect('/auth/login');
            // }

            // $phrase = Session::get('phrase');

            // if ($captcha != $phrase) {
            //     $this->flash->error('Captcha tidak sesuai');
            //     return Response::redirect('/' . $pdamidDirektori . '/auth/login');
            // }

            $dataTest = UsersModel::find()->toArray();

            $user = UsersModel::findFirst([
                'conditions' => 'username = :username:',
                'bind' => [
                    'username' => $usernamePost
                ]
            ]);

            if (!isset($user->username)) {
                $this->flash->error('Username salah atau tidak ditemukan');
                return Response::redirect('/auth/login');
            }

            if ($rememberUsername == "on") {
                // Set cookie untuk 30 hari
                setcookie(
                    'remembered_username',    // nama cookie
                    $usernamePost,               // nilai
                    [
                        'expires' => time() + (86400 * 30), // 30 hari
                        'path' => '/',
                        'secure' => true,    // hanya HTTPS
                        'httponly' => true,  // tidak bisa diakses via JavaScript
                        'samesite' => 'Strict'
                    ]
                );
            } else {
                // Hapus cookie
                setcookie('remembered_username', '', [
                    'expires' => time() - 3600,
                    'path' => '/'
                ]);
            }

            $hasilUser = $user->toArray();

            $password = $hasilUser['password'];
            $pass_enc = $hasilUser['pass_enc'];

            if (!Security::checkHash($passwordPost, $password)) {
                $this->flash->error('Password salah');
                return Response::redirect('/auth/login');
            }

            if ($passwordPost != decrypt($pass_enc, $this->config->appKey)) {
                $this->flash->error('Tidak lolos keamanan enkripsi');
                return Response::redirect('/auth/login');
            }

            $userdata = array(
                'id'            => $user->id,
                'username'      => $user->username,
                'nama'          => $user->nama,
                'password'      => $user->password,
                'pass_enc'      => $user->pass_enc,
                'id_role'       => $user->id_role,
                'state'         => $user->state,
                'periode'       => $periodePost,
                'role_id'       => $user->role->id,
                'role_nama'     => $user->role->role,
                'role_status'   => $user->role->status,
                'dir_dashboard' => $user->role->dir_dashboard
            );

            $pdam = MainModel::findFirst([
                'conditions' => "id = :id: ",
                'bind' => [
                    'id' => $pdamidPost
                ]
            ]);

            $m_periode = substr($periodePost, 4, 6);
            $y_periode = substr($periodePost, 0, 4);

            // OLD WAY (Raw SQL - Rawan SQL Injection):
            // $sql = "SET NOCOUNT ON; EXEC [akuntansi].sp_proses_periode 
            //                 @vm   = " . $m_periode . ",
            //                 @vy   = " . $y_periode . ";
            // ";
            // $resultInsert = $this->db->fetchOne($sql);

            // NEW WAY (Menggunakan Library DatabaseSP - Aman & Mudah):
            $sp = SPHelper::fromPhalcon($this->db, 'mysql');
            // Enable query log untuk debugging
            $sp->getDatabaseSP()->enableQueryLog();

            try {
                $resultInsert = $sp->call('sp_proses_periode', [
                    'vm' => $m_periode,
                    'vy' => $y_periode
                ])->fetchOne();
            } catch (\Exception $e) {
                // Log query untuk debugging
                $queryLog = $sp->getDatabaseSP()->getQueryLog();
                error_log("DatabaseSP Query Log: " . print_r($queryLog, true));
                error_log("Error: " . $e->getMessage());
                throw $e;
            }

            // Untuk sementara masih pakai cara lama, bisa diganti dengan cara baru di atas
            // $sql = "SET NOCOUNT ON; EXEC [akuntansi].sp_proses_periode 
            //                 @vm   = " . $m_periode . ",
            //                 @vy   = " . $y_periode . ";
            // ";
            // $resultInsert = $this->db->fetchOne($sql);



            // Ambil Pada Periode Yang Dipilih
            $finDataPeriodeAktif = PeriodeAktifModel::findFirst([
                'conditions' => "periode_m = :periode_m: AND periode_y = :periode_y:",
                'bind' => [
                    'periode_m' => $m_periode,
                    'periode_y' => $y_periode
                ]
            ]);

            if ($finDataPeriodeAktif) {
                $valPeriodeIsClosed = $finDataPeriodeAktif->is_closed;
            } else {
                $valPeriodeIsClosed = 0;
            }


            if ($valPeriodeIsClosed == 1) {
                $isPeriodeAktifGlobal = 0;
            } else {
                $isPeriodeAktifGlobal = 1;
            }


            Session::set('user', $userdata);
            Session::set('pdam', $pdam);
            Session::set('periode', $periodePost);
            Session::set('m_periode', $m_periode);
            Session::set('y_periode', $y_periode);
            Session::set('tahun', date('Y'));
            Session::set('isPeriodeAktifGlobal', $isPeriodeAktifGlobal);

            // Cek apakah ada halaman yang dituju sebelumnya (intended URL)
            // Jika tidak ada, default ke halaman dashboard

            // $intended = Session::get('intended') ?? '/' . $pdam->direktori . '/dashboard';
            $intended = Session::get('intended') ?? '/panel/hotel/dashboard';

            // Hapus intended URL dari session karena sudah tidak diperlukan
            Session::remove('intended');

            // // Redirect user ke halaman yang dituju
            return Response::redirect($intended);
        }
    }

    /**
     * @routeGet('/logout')
     * @middleware('RequireUser')
     */
    public function logoutAction()
    {
        $dir = $this->session->pdam->direktori;
        Session::remove('user');
        Session::remove('pdam');
        return Response::redirect($dir . '/auth/login');
    }
}
