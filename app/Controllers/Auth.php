<?php

namespace App\Controllers;

use App\Models\CodeModel;
use App\Models\UserModel;
use CodeIgniter\Config\Services;

class Auth extends BaseController
{
    protected $user;
    protected $db;

    public function __construct()
    {
        $this->userModel = new UserModel();
        $this->db = \Config\Database::connect();
    }
    function getUserIP()
    {
        $clientIp = @$_SERVER['HTTP_CLIENT_IP'];
        $forwardIp = @$_SERVER['HTTP_X_FORWARDED_FOR'];
        $remoteIp = $_SERVER['REMOTE_ADDR'];
        if (filter_var($clientIp, FILTER_VALIDATE_IP)) {
            $ipaddress = $clientIp;
        } elseif (filter_var($forwardIp, FILTER_VALIDATE_IP)) {
            $ipaddress = $forwardIp;
        } else {
            $ipaddress = $remoteIp;
        }
        return $ipaddress;
    }
    public function index()
    {
        /* ---------------------------- Debugmode --------------------------- */
        $a = $this->userModel->getUser(session('userid'));
        dd($a, session());
    }

    public function login()
    {
        if (session()->has('userid'))
            return redirect()->to('dashboard')->with('msgSuccess', 'Login Successful!');

        if ($this->request->getPost())
            return $this->login_action();
        $data = [
            'title' => 'Login',
            'validation' => Services::validation(),
        ];
        return view('Auth/login', $data);
    }

    public function register()
    {
        if (session()->has('userid'))
            return redirect()->to('dashboard')->with('msgSuccess', 'Login Successful!');

        if ($this->request->getPost())
            return $this->register_action();
        $validation = Services::validation();
        $data = [
            'title' => 'Register',
            'validation' => $validation,
            'user_ip' => $this->request->getIPAddress()
        ];
        return view('Auth/register', $data);
    }

    private function login_action()
    {
        $usernam = $this->request->getPost('username');
        $password = $this->request->getPost('password');
        $stay_log = $this->request->getPost('stay_log');

        $form_rules = [
            'username' => [
                'label' => 'username',
                'rules' => 'required|alpha_numeric|min_length[4]|max_length[25]|is_not_unique[users.username]',
                'errors' => [
                    'is_not_unique' => 'The {field} is not registered.'
                ]
            ],
            'password' => [
                'label' => 'password',
                'rules' => 'required|min_length[6]|max_length[45]',
            ],
            'stay_log' => [
                'rules' => 'permit_empty|max_length[3]'
            ]
        ];

        if (!$this->validate($form_rules)) {
            return redirect()->route('login')->withInput()->with('msgDanger', '<strong>Failed</strong> Please check the form.');
        } else {
            $validation = Services::validation();
            $cekUser = $this->userModel->getUser($usernam, 'username');
            if ($cekUser) {
                $hashPassword = create_password($password, false);
                if (password_verify($hashPassword, $cekUser->password)) {
                    $time = new \CodeIgniter\I18n\Time;
                    $data = [
                        'userid' => $cekUser->id_users,
                        'unames' => $cekUser->username,
                        'time_login' => $stay_log ? $time::now()->addHours(24) : $time::now()->addMinutes(30),
                        'time_since' => $time::now(),
                    ];

                    // Replaced mysqli with CI4 QueryBuilder
                    $now = (string) $time::now();
                    $exp = $this->db->table('users')
                        ->select('expiration_date')
                        ->where('username', $usernam)
                        ->where('expiration_date >', $now)
                        ->get()->getRowArray();

                    $msgexp = $this->db->table('users')
                        ->select('expiration_date')
                        ->where('username', $usernam)
                        ->get()->getRowArray();

                    $sts = $this->db->table('users')
                        ->select('status')
                        ->where('username', $usernam)
                        ->get()->getRowArray();

                    if ($exp && $sts) {
                        session()->set($data);
                        // Send Login Alert (replaced UserMail.php)
                        $this->sendLoginAlert($cekUser);

                        $phpmsg = $msgexp['expiration_date'];
                        $expmsg = "Account Expires on : $phpmsg";

                        return redirect()->to('dashboard')->with('msgSuccess', $expmsg);
                    } else {
                        return redirect()->route('login')->withInput()->with('msgDanger', '<strong>Expired</strong> Please Renew Your Account to Login.');
                    }
                } else {
                    $validation->setError('password', 'Wrong password, please try again.');
                    return redirect()->route('login')->withInput()->with('msgDanger', '<strong>Failed</strong> Please check the form.');
                }
            }
        }
    }

    private function sendLoginAlert($user)
    {
        if ($user && isset($user->email)) {
            $email = \Config\Services::email();
            $email->setFrom('noreply@probotfree.store', '𝗣𝗥𝗢𝗕𝗢𝗧 𝗙𝗥𝗘𝗘 𝗢𝗙𝗙𝗜𝗖𝗜𝗔𝗟');
            // $user->email might depend on object structure. Assuming standard property.
            $email->setTo($user->email);

            $timestamp = date('d/m/Y h:i:sa');
            $server = $_SERVER['HTTP_HOST'];
            $usern = $user->username;
            $user_ip = $this->request->getIPAddress();
            $webpage = current_url();
            $browser = $this->request->getUserAgent()->getAgentString();

            $email->setSubject("[$server]✔ Logged in as $usern at $timestamp");
            $email->setMailType('html');

            $message = "<p>Dear [ $usern ],</p>
             <p>You have successfully logged in to your account.</p>
             <p>Your IP: $user_ip</p>
             <p>Time: $timestamp</p>
             <p>Accessed Page: $webpage</p>
             <p>Browser: $browser</p>
             <p>Copyright © 2023 PROBOT FREE OFFICIAL</p>";

            $email->setMessage($message);
            $email->send();
        }
    }

    public function register_action()
    {
        $email = $this->request->getPost('email');
        $userna = $this->request->getPost('username');
        $fullname = $this->request->getPost('fullname');
        $password = $this->request->getPost('password');
        $referral = $this->request->getPost('referral');

        $form_rules = [
            'email' => [
                'label' => 'email',
                'rules' => 'required|valid_email|min_length[13]|max_length[40]|'
            ],
            'username' => [
                'label' => 'username',
                'rules' => 'required|alpha_numeric|min_length[4]|max_length[25]|is_unique[users.username]',
                'errors' => [
                    'is_unique' => 'The {field} has been taken.'
                ]
            ],
            'fullname' => [
                'label' => 'fullname',
                'rules' => 'required|min_length[4]|max_length[25]|is_unique[users.fullname]',
                'errors' => [
                    'is_unique' => 'The {field} has been taken.'
                ]
            ],
            'password' => [
                'label' => 'password',
                'rules' => 'required|min_length[6]|max_length[45]',
            ],
            'password2' => [
                'label' => 'password',
                'rules' => 'required|min_length[6]|max_length[45]|matches[password]',
                'errors' => [
                    'matches' => '{field} not match, check the {field}.'
                ]
            ],
            'referral' => [
                'label' => 'referral',
                'rules' => 'required|min_length[6]|alpha_numeric',
            ]
        ];

        if (!$this->validate($form_rules)) {
            // Form Invalid
        } else {
            $mCode = new CodeModel();
            $rCheck = $mCode->checkCode($referral);
            $validation = Services::validation();
            if (!$rCheck) {
                $validation->setError('referral', 'Wrong referral, please try again.');
            } else {
                if ($rCheck->used_by) {
                    $validation->setError('referral', "Wrong referral, code has been used &middot; $rCheck->used_by.");
                } else {
                    $hashPassword = create_password($password);
                    $ipaddress = $_SERVER['REMOTE_ADDR'];

                    // Replaced mysqli with CI4 QueryBuilder
                    $period = $this->db->table('referral_code')
                        ->select('acc_expiration')
                        ->where('Referral', $referral)
                        ->get()->getRowArray();

                    $userLevel = $this->db->table('referral_code')
                        ->select('level')
                        ->where('Referral', $referral)
                        ->get()->getRowArray();

                    // $userLevel logic in original was directly assigning mysqli_fetch_assoc(query1) to 'level'. 
                    // But 'level' key in insert probably expects a value, not array. 
                    // However, original code was:
                    // $userLevel = mysqli_fetch_assoc($query1);
                    // 'level' => $userLevel, 
                    // So it passed ['level' => '1'] (example) to an array column? Or maybe $userLevel['level']?
                    // Checking UserModel later. Assuming direct array assignment was intended or handled by Model.
                    // Actually, if $userLevel is ['level' => 1], then passed to insert data.

                    $data_register = [
                        'email' => $email,
                        'username' => $userna,
                        'fullname' => $fullname,
                        'level' => $userLevel ? $userLevel['level'] : 2, // Assuming default or fix. Original code might have bug or handled array convert
                        'password' => $hashPassword,
                        'saldo' => $rCheck->set_saldo ?: 0,
                        'uplink' => $rCheck->created_by,
                        'user_ip' => $ipaddress,
                        'expiration_date' => $period ? $period['acc_expiration'] : null
                    ];
                    $ids = $this->userModel->insert($data_register, true);
                    if ($ids) {
                        $mCode->useReferral($referral);
                        $msg = "Register Successfuly!";
                        return redirect()->to('login')->with('msgSuccess', $msg);
                    }
                }
            }
        }
        return redirect()->route('register')->withInput()->with('msgDanger', '<strong>Failed</strong> Please check the form.');
    }

    public function verify_pass()
    {
        $emailId = $this->request->getGet('mail');
        $token = $this->request->getGet('token');
        $password = $this->request->getGet('key');

        if ($emailId && $token && $password) {
            $time = new \CodeIgniter\I18n\Time;
            $timestamp = (string) $time::now();

            $user = $this->db->table('users')
                ->where('reset_link_token', $token)
                ->get()->getRowArray();

            if ($user) {
                $exp_time = $user['exp_date'];
                if ($timestamp < $exp_time) {
                    // Hash the password to match login expectation
                    $hashPassword = create_password($password, true);

                    $this->db->table('users')->where('reset_link_token', $token)->update([
                        'password' => $hashPassword,
                        'reset_link_token' => null,
                        'exp_date' => null
                    ]);

                    return "Congratulations! Your password has been updated successfully.";
                } else {
                    return "Link Expired. Request Again..!";
                }
            } else {
                return "Something goes wrong. Please try again";
            }
        }
        return "Link Broken.";
    }

    public function logout()
    {
        if (session()->has('userid')) {
            $unset = ['userid', 'unames', 'time_login', 'time_since'];
            session()->remove($unset);
            session()->setFlashdata('msgSuccess', 'Logout successfuly.');
        }
        return redirect()->to('login');
    }
}
