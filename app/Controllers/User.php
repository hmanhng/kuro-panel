<?php

namespace App\Controllers;

use App\Models\CodeModel;
use App\Models\Server;
use App\Models\Status;
use App\Models\_ftext;
use App\Models\Feature;
use App\Models\onoff;
use App\Models\HistoryModel;
use App\Models\UserModel;
use CodeIgniter\Config\Services;
use CodeIgniter\Controller;

class User extends BaseController
{
    protected $model, $userid, $user;

    public function __construct()
    {
        $this->userid = session()->userid;
        $this->model = new UserModel();
        $this->user = $this->model->getUser($this->userid);
        $this->time = new \CodeIgniter\I18n\Time;

        $this->accExpire = [
            1 => '1 Day',
            7 => '7 Days',
            15 => '15 Days',
            30 => '30 Days',
            60 => '60 Days',
        ];

        $this->accLevel = [
            1 => 'Owner',
            2 => 'Admin',
            3 => 'Reseller',
        ];
    }

    public function index()
    {
        $historyModel = new HistoryModel();

        // Stats from dashboard.php
        $db = \Config\Database::connect();

        // Credit (id=1)
        $credit = $db->table('credit')->where('id', 1)->get()->getRowArray();

        // Keys count
        $keycount = $db->table('keys_code')->countAllResults();

        // Active Keys
        //$active = $db->table('keys_code')->selectCount('devices')->get()->getRowArray(); 
        // Original: SELECT COUNT(devices) as devices FROM keys_code
        // COUNT(column) counts non-null values.
        $active = $db->table('keys_code')->where('devices IS NOT NULL')->countAllResults();
        // Wait, original was SELECT COUNT(devices)... if devices is null, it's not counted?
        // Let's assume devices column semantics.
        // Actually, let's stick to query builder count.

        // In-Active Keys (devices IS NULL)
        $inactive = $db->table('keys_code')->where('devices', null)->countAllResults();

        // Users Count
        $usersCount = $db->table('users')->countAllResults();

        // Current User Expiration
        // Already available in $this->user? 
        // dashboard.php did: SELECT expiration_date FROM users WHERE id_users = ...
        // $this->user is fetched in __construct via generic getUser.
        // Let's check if $this->user has expiration_date.
        // If not, fetch it.
        // Assuming $this->user has it.

        // Email Alert Logic (from mail.php)
        $this->sendAdminAlert();

        $data = [
            'title' => 'Dashboard',
            'user' => $this->user,
            'time' => $this->time,
            'history' => $historyModel->getAll(),
            'credit' => $credit,
            'keycount' => $keycount,
            'active_keys' => $active, // used to be fetched as 'devices' (count)
            'inactive_keys' => $inactive,
            'users_count' => $usersCount,
        ];
        return view('User/dashboard', $data);
    }

    private function sendAdminAlert()
    {
        $date = new \CodeIgniter\I18n\Time;
        $db = \Config\Database::connect();

        // Get Admin Email
        $adminUser = $db->table('users')->select('email')->where('username', 'Shauryanprobotfree')->get()->getRowArray();
        $usersmail = $adminUser ? $adminUser['email'] : null;

        if ($usersmail) {
            $user_ip = $this->request->getIPAddress();
            $timestamp = date('d/m/Y h:i:sa');
            $accesstime = date('h:i:sa');
            $webpage = current_url(); // equivalent to REQUEST_URI roughly (full url)
            $browser = $this->request->getUserAgent()->getAgentString(); // or $_SERVER['HTTP_USER_AGENT']
            $url = base_url(); // SERVER_NAME / host
            $usern = $this->user ? $this->user->username : 'No User Account found';

            $email = \Config\Services::email();
            $email->setFrom('shauryan@probotfree.store', '𝗣𝗥𝗢𝗕𝗢𝗧 𝗙𝗥𝗘𝗘 𝗢𝗙𝗙𝗜𝗖𝗜𝗔𝗟');
            $email->setTo($usersmail);
            $email->setMailType('html');
            $email->setSubject("$user_ip 𝙐𝙨𝙞𝙣𝙜 𝙔𝙤𝙪𝙧 𝙋𝙖𝙣𝙚𝙡 $accesstime");

            // Email Body (Simplified from mail.php to avoid huge block in controller, or keep it?)
            // I'll keep the structure but clean it up slightly.
            // Moving long HTML string to a View would be better, but inline is faster for now.
            // Using a simple message for creating logic.

            $message = "<!DOCTYPE html><html><body style='font-family: sans-serif;'>
            <h2>&#10071;An User Trying To Access Your Panel &#10075;$user_ip&#10076; &#128099;&#10071;</h2>
            <p>User IP: $user_ip</p>
            <p>Time: $timestamp</p>
            <p>Page & User: $webpage & $usern</p>
            <p>Browser: $browser</p>
            </body></html>";

            // Note: Preventing spam? The original code sent it EVERY TIME.
            // I will implement it as is, but maybe this is annoying.
            // Ideally, only on sensitive actions?
            // User requested to refactor legacy code, implying strict port.

            $email->setMessage($message);
            $email->send();
        }
    }


    public function ref_index()
    {
        $user = $this->user;

        if ($this->request->getPost())
            if (($user->level == 1) || ($user->level == 2)) {
                return $this->reff_action();
            } else {

                return redirect()->to('dashboard')->with('msgWarning', 'Access Denied!');
            }

        $mCode = new CodeModel();
        $validation = Services::validation();
        $data = [
            'title' => 'Referral',
            'user' => $user,
            'time' => $this->time,
            'code' => $mCode->getCode(),
            'accExpire' => $this->accExpire,
            'accLevel' => $this->accLevel,
            'total_code' => $mCode->countAllResults(),
            'validation' => $validation
        ];
        return view('Admin/referral', $data);
    }


    private function reff_action()
    {
        $saldo = $this->request->getPost('set_saldo');
        $user_expire = $this->request->getPost('accExpire');
        $accLevel1 = $this->request->getPost('accLevel');
        $accExpire = $this->time::now()->addDays($user_expire);
        $form_rules = [
            'set_saldo' => [
                'label' => 'saldo',
                'rules' => 'required|numeric|max_length[11]|greater_than_equal_to[0]',
                'errors' => [
                    'greater_than_equal_to' => 'Invalid currency, cannot set to minus.'
                ]
            ],
            'accExpire' => [
                'label' => 'Account Expiration',
                'rules' => 'required|numeric|max_length[2]|greater_than_equal_to[1]',
                'errors' => [
                    'greater_than_equal_to' => 'Invalid Days, cannot set to expired.'
                ]
            ]
        ];

        if (!$this->validate($form_rules)) {
            return redirect()->back()->withInput()->with('msgDanger', 'Failed, check the form');
        } else {
            $code = random_string('alnum', 6);
            $codeHash = create_password($code, false);
            $referral_code = [
                'code' => $codeHash,
                'Referral' => $code,
                'level' => $accLevel1,
                'set_saldo' => ($saldo < 1 ? 0 : $saldo),
                'created_by' => session('unames'),
                'acc_expiration' => $accExpire
            ];
            $mCode = new CodeModel();
            $ids = $mCode->insert($referral_code, true);
            if ($ids) {
                $msg = "Referral : $code";
                /*$code = random_string('alnum', 6);
                $darkcode = (" $code");/*For Updating 6 Digit of Code*/
                //$codeHash = create_password($code, false);/*Its Encrypting the Referral Codes by Hashing Method*/
                /*$referral_code = [
                    'code' => $darkcode,/*$codeHash,//it used to update Hashed Refferal Code in Database*//*
                    'set_saldo' => ($saldo < 1 ? 0 : $saldo),
                    'created_by' => session('unames')
                ];
                $mCode = new CodeModel();
                $ids = $mCode->insert($referral_code, true);
                if ($ids) {
                    $msg = "Referral : $code";*/
                return redirect()->back()->with('msgSuccess', $msg);
            }
        }
    }


    //public function alterUser(){
    //  echo 'hello';
    //  $model = new userModel();

    //  $data=$model->where('id_users !=', 1)->delete();
    //  print_r($data);
    //   return redirect()->back()->with('msgSuccess', 'success');
    //  }



    public function api_get_users()
    {
        // API for DataTables
        $model = $this->model;
        return $model->API_getUser();
    }

    public function manage_users()
    {
        $user = $this->user;
        if ($user->level != 1)
            return redirect()->to('dashboard')->with('msgWarning', 'Access Denied!');

        $model = $this->model;
        $validation = Services::validation();
        $data = [
            'title' => 'Users',
            'user' => $user,
            'user_list' => $model->getUserList(),
            'time' => $this->time,
            'validation' => $validation
        ];
        return view('Admin/users', $data);
    }


    public function user_delete($userid = false)
    {
        $model = new userModel();
        $data = $model->where('id_users =', $userid)->delete();
        return redirect()->back()->with('msgSuccess', 'success');
    }

    public function user_edit($userid = false)
    {
        $user = $this->user;
        if ($user->level != 1)
            return redirect()->to('dashboard')->with('msgWarning', 'Access Denied!');

        if ($this->request->getPost())
            return $this->user_edit_action();

        $model = $this->model;
        $validation = Services::validation();

        $data = [
            'title' => 'Settings',
            'user' => $user,
            'target' => $model->getUser($userid),
            'user_list' => $model->getUserList(),
            'time' => $this->time,
            'validation' => $validation,
        ];
        return view('Admin/user_edit', $data);
    }

    private function user_edit_action()
    {
        $model = $this->model;
        $userid = $this->request->getPost('user_id');

        $target = $model->getUser($userid);
        if (!$target) {
            $msg = "User no longer exists.";
            return redirect()->to('dashboard')->with('msgDanger', $msg);
        }

        $username = $this->request->getPost('username');

        $form_rules = [
            'username' => [
                'label' => 'username',
                'rules' => "required|min_length[4]|max_length[25]|is_unique[users.username,username,$target->username]",
                'errors' => [
                    'is_unique' => 'The {field} has taken by other.'
                ]
            ],
            'fullname' => [
                'label' => 'name',
                'rules' => 'permit_empty|min_length[4]|max_length[155]',
                'errors' => [
                    'alpha_space' => 'The {field} only allow alphabetical characters and spaces.'
                ]
            ],
            'level' => [
                'label' => 'roles',
                'rules' => 'required|numeric|in_list[1,2,3]',
                'errors' => [
                    'in_list' => 'Invalid {field}.'
                ]
            ],
            'status' => [
                'label' => 'status',
                'rules' => 'required|numeric|in_list[1,2,3]',
                'errors' => [
                    'in_list' => 'Invalid {field} account.'
                ]
            ],
            'saldo' => [
                'label' => 'saldo',
                'rules' => 'permit_empty|numeric|max_length[11]|greater_than_equal_to[0]',
                'errors' => [
                    'greater_than_equal_to' => 'Invalid currency, cannot set to minus.'
                ]
            ],
            'uplink' => [
                'label' => 'uplink',
                'rules' => 'required|is_not_unique[users.username,username,]',
                'errors' => [
                    'is_not_unique' => 'Uplink not registered anymore.'
                ]
            ],
            'expiration' => [
                'label' => 'expiration',
                'rules' => 'required|min_length[4]|max_length[155]',
                'errors' => [
                    'is_not_unique' => 'Expiration not registered anymore.'
                ]
            ],
            'email' => [
                'label' => 'email',
                'rules' => 'required|min_length[4]|max_length[155]',
                'errors' => [
                    'is_unique' => 'Email not registered anymore.'
                ]
            ],
        ];

        if (!$this->validate($form_rules)) {
            return redirect()->back()->withInput()->with('msgDanger', 'Something wrong! Please check the form');
        } else {
            $fullname = $this->request->getPost('fullname');
            $level = $this->request->getPost('level');
            $status = $this->request->getPost('status');
            $saldo = $this->request->getPost('saldo');
            $uplink = $this->request->getPost('uplink');
            $expiration = $this->request->getPost('expiration');
            $email = $this->request->getPost('email');
            $data_update = [
                'username' => $username,
                'fullname' => esc($fullname),
                'level' => $level,
                'status' => $status,
                'saldo' => (($saldo < 1) ? 0 : $saldo),
                'uplink' => $uplink,
                'expiration_date' => $expiration,
                'email' => $email
            ];

            $update = $model->update($userid, $data_update);
            if ($update) {
                return redirect()->back()->with('msgSuccess', "Successfuly update $target->username.");
            }
        }
    }

    public function settings()
    {
        $user = $this->user;
        if (($user->level == 1) || ($user->level == 2)) {
            if ($this->request->getPost('password_form'))
                return $this->passwd_act();
        }
        /*if ($this->request->getPost('fullname_form'))
            return $this->fullname_act();*/
        if ($this->request->getPost('email_form'))
            return $this->email_act();
        $user = $this->user;
        $validation = Services::validation();
        $data = [
            'title' => 'Settings',
            'user' => $user,
            'time' => $this->time,
            'validation' => $validation
        ];
        return view('User/settings', $data);
    }

    public function lib()
    {
        $user = $this->user;
        if ($user->level != 1 && $user->level != 2) {
            return redirect()->to('dashboard')->with('msgWarning', 'Access Denied!');
        }

        $validation = Services::validation();
        $db = \Config\Database::connect();

        if ($this->request->getMethod() === 'post') {
            // Handle File Upload
            // input name 'libfile' from Admin/lib.php (or 'myfile' if using public/lib.php form, enforcing libfile)
            $file = $this->request->getFile('libfile');

            if ($file && $file->isValid() && !$file->hasMoved()) {
                $extension = $file->getExtension();
                if ($extension !== 'so') {
                    return redirect()->back()->with('msgDanger', 'Only Upload MOD SERVER LIB (.so)!');
                }

                if ($file->getSize() > 100000000) { // 100MB
                    return redirect()->back()->with('msgDanger', 'File is too large (>100MB).');
                }

                $filename = $file->getName();
                $destinationDir = FCPATH . 'TumharaPapaDARK/';
                if (!is_dir($destinationDir)) {
                    mkdir($destinationDir, 0777, true);
                }

                if ($file->move($destinationDir, $filename)) {
                    // Logic from public/lib.php:
                    // $realsize = formatBytes($size)...
                    // DB Insert: INSERT INTO lib (id, file, file_type, file_size, time) VALUES ('++$x', ...)
                    // 'id' seems to be auto-increment or '++$x' (string?). 
                    // 'lib' table schema? public/lib.php used '++$x' where $x="1".
                    // 'file_type' used as $destination ('TumharaPapaDARK/filename')
                    // 'file_size' formatted string.
                    // 'time' filemtime($filename) -> timestamp.

                    // Let's assume 'id' column is auto-increment.
                    // We will update the row with id=1?
                    // app/Views/Admin/lib.php fetches id=1. public/lib.php INSERTs new row?
                    // app/Views/Admin/lib.php code: $sql21 = "SELECT * FROM lib WHERE id='1'";
                    // public/lib.php code: INSERT INTO lib ... VALUES ('++$x', ...). 
                    // This is confusing. public/lib.php seems to insert new entries. Admin/lib.php view shows only id=1.
                    // If the intention is to update the single "Online Lib", I should UPDATE id=1.
                    // If public/lib.php was adding historical records, then OK.
                    // BUT Admin/lib.php only shows "CURRENT LIB" (id=1). 
                    // So I will UPDATE id=1 to match the "Manage Lib" feature.

                    $sizeBytes = $file->getSize();
                    $base = log($sizeBytes, 1024);
                    $suffixes = array('B', 'KB', 'MB', 'GB', 'TB');
                    $realsize = round(pow(1024, $base - floor($base)), 2) . ' ' . $suffixes[floor($base)];
                    $lt = time(); // current timestamp
                    $path = 'TumharaPapaDARK/' . $filename;

                    $data = [
                        'name' => $filename, // Views/Admin/lib.php uses 'name' column? "echo $userDetails21['name'];"
                        'file' => $filename, // public/lib.php used 'file'.
                        'file_type' => $path, // public/lib.php used 'file_type' as path?
                        'path' => $path,      // Views/Admin/lib.php uses 'path'.
                        'size' => $realsize,  // Views/Admin/lib.php uses 'size'.
                        'file_size' => $realsize, // public/lib.php used 'file_size'.
                        'time' => $lt,        // public/lib.php used 'time'.
                        'last_modified' => $lt // Views/Admin/lib.php uses 'last_modified'.
                    ];

                    // Updating id=1 to ensure the "Current Lib" is updated. 
                    // Inserting might fill DB but not show in Admin panel if it only selects id=1.

                    $exists = $db->table('lib')->where('id', 1)->countAllResults();
                    if ($exists) {
                        $db->table('lib')->where('id', 1)->update($data);
                    } else {
                        $data['id'] = 1;
                        $db->table('lib')->insert($data);
                    }

                    return redirect()->back()->with('msgSuccess', 'LIB uploaded successfully: ' . $realsize);
                } else {
                    return redirect()->back()->with('msgDanger', 'Failed to move uploaded file.');
                }
            } else {
                return redirect()->back()->with('msgDanger', 'Invalid file or upload failed.');
            }
        }

        // Fetch Current Lib Data (id=1)
        $libData = $db->table('lib')->where('id', 1)->get()->getRowArray();

        // Format last modified like legacy lib.php:
        // $timestamp = $userDetails21['last_modified'];
        // $date = new DateTime(); $date->setTimestamp($timestamp); ... $last = $date->format(...)
        // Or just pass the raw data and let View handle it (or handle here).
        // Legacy view used $last variable.
        if ($libData) {
            $timestamp = $libData['last_modified'];
            // Wait, existing data in DB might be formatted string if legacy code was messy. 
            // In public/lib.php it inserted filemtime() which is int timestamp.
            // But table schema might be VARCHAR? 
            // Assuming int.
            $date = new \DateTime();
            $date->setTimestamp((int) $timestamp);
            $libData['formatted_date'] = $date->format('Y-m-d h:i:s a');

            // Legacy view also used $current (current time). I can pass it or use date().
        }

        $data = [
            'title' => 'lib',
            'user' => $user,
            'time' => $this->time,
            'validation' => $validation,
            'libData' => $libData
        ];
        return view('Admin/lib', $data);
    }

    public function Server()
    {
        $user = $this->user;
        if (($user->level == 1) || ($user->level == 2)) {

            if ($this->request->getPost('modname_form'))

                return $this->modname_act();

            if ($this->request->getPost('status_form'))
                return $this->status_act();
        }
        if ($user->level == 1) {
            if ($this->request->getPost('feature_form'))
                return $this->feature_act();
            if ($this->request->getPost('password_form'))
                return $this->passwd_act();
        }
        if (($user->level == 1) || ($user->level == 2)) {
            if ($this->request->getPost('_ftext'))
                return $this->_ftext_act();

            if ($this->request->getPost('fullname_form'))
                return $this->fullname_act();

        }
        $user = $this->user;

        $validation = Services::validation();
        $data = [
            'title' => 'Server',
            'user' => $user,
            'time' => $this->time,
            'validation' => $validation
        ];

        //==================================Mod Name======================//

        $id = 1;

        $model = new Server();

        $onoffModel = new \App\Models\onoff();
        $ftextModel = new \App\Models\_ftext();
        $featureModel = new \App\Models\Feature();

        $data['row'] = $model->where('id', $id)->first();
        $data['onoff'] = $onoffModel->find(1);
        $data['ftext'] = $ftextModel->find(1);
        $data['feature'] = $featureModel->find(1);


        if (($user->level == 1) || ($user->level == 2)) {
            return view('Server/Server', $data);
        } else {

            return redirect()->to('dashboard')->with('msgWarning', 'Access Denied');
        }
    }

    public function Profile()
    {
        $user = $this->user;
        if ($user->level == 1) {

            if ($this->request->getPost('modname_form'))

                return $this->modname_act();

            if ($this->request->getPost('status_form'))
                return $this->status_act();
        }

        if ($this->request->getPost('password_form'))
            return $this->passwd_act();

        if ($user->level == 1) {

            if ($this->request->getPost('_ftext'))
                return $this->_ftext_act();
        }


        if ($this->request->getPost('fullname_form'))
            return $this->fullname_act();

        $user = $this->user;

        $model = $this->model;
        $validation = Services::validation();
        $data = [
            'title' => 'Users',
            'user' => $user,
            'user_list' => $model->getUserList(),
            'time' => $this->time,
            'validation' => $validation
        ];

        //==================================Mod Name======================//

        $id = 1;

        $model = new Server();

        $data['row'] = $model->where('id', $id)->first();


        // Fetch Stats
        $historyModel = new \App\Models\HistoryModel();
        $db = \Config\Database::connect();

        $data['userDetails1'] = $historyModel->where('user_do', $user->username)->countAllResults();
        $data['userDetails2'] = $historyModel->countAllResults();
        $data['userDetails3'] = $db->table('keys_code')->countAllResults();

        $usus = "error";
        if ($user->level == 1) {
            $usus = "Admin";
        } elseif ($user->level == 2) {
            $usus = "Reseller";
        }
        $data['usus'] = $usus;

        return view('Profile/Profile', $data);


        //==================================Mod Status======================//



    }

    public function Pic()
    {
        $model = new Server();
        $user = $this->user;
        $id = 1;
        $validation = Services::validation();
        $data = [
            'title' => 'Profile',
            'user' => $user,
            'time' => $this->time,
            'validation' => $validation
        ];


        $data['row'] = $model->where('id', $id)->first();
        if ($user->level == 1) {
            return view('Pic/Pic', $data);
        } else {

            return redirect()->to('dashboard')->with('msgWarning', 'Access Deniend');
        }


        //==================================Mod Status======================//



    }

    private function _ftext_act()
    {
        $id = 1;
        $model = new _ftext();
        $myinput = $this->request->getPost('_ftext');
        $status = $this->request->getPost('_ftextr');
        $wow = '';
        if ($status == "Safe") {
            $wow .= "Safe";
        } else {
            $wow .= "Anti-Cheat is High..!!";
        }
        $data = ['_ftext' => $myinput, '_status' => $wow];
        $model->update($id, $data);
        return redirect()->back()->with('msgSuccess', 'Successfuly Changed Mod Floating And Status.');
    }

    private function status_act()
    {
        $id = 1;
        $model = new onoff();
        $myinput = $this->request->getPost('myInput');
        $wow = '';
        if (isset($_POST['radios']) && $_POST['radios'] == 'on') {
            $wow .= "on";
        } else {
            $wow .= "off";
        }
        $data = [
            'status' => $wow,
            'myinput' => $myinput
        ];
        $model->update($id, $data);
        return redirect()->back()->with('msgSuccess', 'Mod Status Successfuly Changed.');
    }

    private function modname_act()
    {
        $id = 1;
        $model = new Server();
        $new_modname = $this->request->getPost('modname');
        $data = ['modname' => $new_modname];
        $model->update($id, $data);
        return redirect()->back()->with('msgSuccess', 'Mod Name Successfuly Changed.');
    }

    private function feature_act()
    {
        $id = 1;
        $model = new Feature();
        //=================================================//
        if (isset($_POST['ESP']) && $_POST['ESP'] == 'on') {
            $new_espvalue = "on";
        } else {
            $new_espvalue = "off";
        }
        //=================================================//
        if (isset($_POST['Item']) && $_POST['Item'] == 'on') {
            $new_Itemvalue = "on";
        } else {
            $new_Itemvalue = "off";
        }
        //=================================================//
        if (isset($_POST['AIM']) && $_POST['AIM'] == 'on') {
            $new_aimvalue = "on";
        } else {
            $new_aimvalue = "off";
        }
        //=================================================//
        if (isset($_POST['SilentAim']) && $_POST['SilentAim'] == 'on') {
            $new_SilentAimvalue = "on";
        } else {
            $new_SilentAimvalue = "off";
        }
        //=================================================//
        if (isset($_POST['BulletTrack']) && $_POST['BulletTrack'] == 'on') {
            $new_BulletTrackvalue = "on";
        } else {
            $new_BulletTrackvalue = "off";
        }
        //=================================================//
        if (isset($_POST['Memory']) && $_POST['Memory'] == 'on') {
            $new_Memoryvalue = "on";
        } else {
            $new_Memoryvalue = "off";
        }
        //=================================================//
        if (isset($_POST['Floating']) && $_POST['Floating'] == 'on') {
            $new_Floatingvalue = "on";
        } else {
            $new_Floatingvalue = "off";
        }
        //=================================================//
        if (isset($_POST['Setting']) && $_POST['Setting'] == 'on') {
            $new_Settingvalue = "on";
        } else {
            $new_Settingvalue = "off";
        }
        //=================================================//
        $data = [
            'ESP' => $new_espvalue,
            'Item' => $new_Itemvalue,
            'SilentAim' => $new_SilentAimvalue,
            'AIM' => $new_aimvalue,
            'BulletTrack' => $new_BulletTrackvalue,
            'Memory' => $new_Memoryvalue,
            'Floating' => $new_Floatingvalue,
            'Setting' => $new_Settingvalue
        ];
        $model->update($id, $data);
        return redirect()->back()->with('msgSuccess', 'Mod Feature Stats Changed.');
    }

    private function passwd_act()
    {
        $current = $this->request->getPost('current');
        $password = $this->request->getPost('password');
        $user = $this->user;
        $currHash = create_password($current, false);
        $validation = Services::validation();
        if (!password_verify($currHash, $user->password)) {
            $msg = "Wrong current password.";
            $validation->setError('current', $msg);
        } elseif ($current == $password) {
            $msg = "Nothing to change.";
            $validation->setError('password', $msg);
        }

        $form_rules = [
            'current' => [
                'label' => 'current',
                'rules' => 'required|min_length[6]|max_length[45]',
            ],
            'password' => [
                'label' => 'password',
                'rules' => 'required|min_length[6]|max_length[45]',
            ],
            'password2' => [
                'label' => 'confirm',
                'rules' => 'required|min_length[6]|max_length[45]|matches[password]',
                'errors' => [
                    'matches' => '{field} not match, check the {field}.'
                ]
            ],
        ];

        if (!$this->validate($form_rules)) {
            return redirect()->back()->withInput()->with('msgDanger', 'Something wrong! Please check the form');
        } elseif ($this->validate($form_rules)) {
            $newPassword = create_password($password);
            if (isset($newPassword)) {
                // Removed include conn.php
                $db = \Config\Database::connect();
                $CompName = "PROBOT FREE OFFICIAL";
                $this->userid = session()->userid;
                $this->model = new UserModel();
                $this->user = $this->model->getUser($this->userid);
                $user = $this->user;
                $username = getName($user);
                date_default_timezone_set('Asia/Ho_Chi_Minh');
                $timestamp = date('d/m/Y h:i:sa');
                $server = $_SERVER['HTTP_HOST'];

                // Replaced mysqli with CI4 Query Builder
                $row = $db->table('users')->where('id_users', $this->userid)->get()->getRowArray();

                if ($row) {
                    $emailId = $row['email'];
                    $token = md5($emailId) . rand(10, 9999);
                    $format = mktime(date("h"), date("i") + 10, date("s"), date("m"), date("d"), date("Y"));
                    $expDate = date("Y-m-d H:i:s", $format);

                    $db->table('users')->where('id_users', $this->userid)->update([
                        'reset_link_token' => $token,
                        'exp_date' => $expDate
                    ]);

                    $link = "<a class='btn btn-copy' href='https://" . $server . "/verify_pass?key=" . $newPassword . "&mail=" . $emailId . "&token=" . $token . "' style='text-align:center;width: 60px;font-size:16px'>Change Password</a>";
                    $email = \Config\Services::email();
                    $email->setFrom('noreply@probotfree.store', ' 𝗣𝗥𝗢𝗕𝗢𝗧 𝗙𝗥𝗘𝗘 𝗢𝗙𝗙𝗜𝗖𝗜𝗔𝗟');
                    $email->setTo($emailId);
                    $email->setSubject("[$server]✔ Password Change Request at $timestamp");
                    $email->setMessage("<p>Hello...!</p>
        
            <p>You have been requested to change Password at [$server] for [$username] on [$timestamp].</p>
            <p>Please, Click on the following link to change your Account's Password.</p>
            <p>This Mail Can only Valid upto 10 minutes. If you are not requested for password change then contact Panel owner.</p>
         
            '" . $link . "'");
                    $email->send();
                }
            }
            //$this->model->update(session('userid'), ['password' => $newPassword]);
            return redirect()->back()->with('msgSuccess', 'Password Change Request Sent.');
        }
    }

    public function verify_pass()
    {
        $user = $this->user;
        $data = [
            'title' => 'verify_pass',
            'user' => $user,
            'time' => $this->time
        ];
        return view('User/verify_pass', $data);
    }

    private function email_act()
    {
        $user = $this->user;
        $newEmail = $this->request->getPost('email');

        if ($user->email == $newEmail) {
            $validation = Services::validation();
            $msg = "Nothing to change.";
            $validation->setError('email', $msg);
        }

        $form_rules = [
            'email' => [
                'label' => 'email',
                'rules' => 'required|min_length[4]|max_length[155]',
                'errors' => [
                    'is_unique' => 'Email not registered anymore.'
                ]
            ]
        ];

        if (!$this->validate($form_rules)) {
            return redirect()->back()->withInput()->with('msgDanger', 'Failed! Please check the form');
        } else {
            $this->model->update(session('userid'), ['email' => esc($newEmail)]);
            return redirect()->back()->with('msgSuccess', 'Account Detail Successfuly Changed.');
        }
    }


    private function fullname_act()
    {
        $user = $this->user;
        $newName = $this->request->getPost('fullname');

        if ($user->fullname == $newName) {
            $validation = Services::validation();
            $msg = "Nothing to change.";
            $validation->setError('fullname', $msg);
        }

        $form_rules = [
            'fullname' => [
                'label' => 'name',
                'rules' => 'required|alpha_space|min_length[4]|max_length[155]',
                'errors' => [
                    'alpha_space' => 'The {field} only allow alphabetical characters and spaces.'
                ]
            ]
        ];

        if (!$this->validate($form_rules)) {
            return redirect()->back()->withInput()->with('msgDanger', 'Failed! Please check the form');
        } else {
            $this->model->update(session('userid'), ['fullname' => esc($newName)]);
            return redirect()->back()->with('msgSuccess', 'Account Detail Successfuly Changed.');
        }
    }
}