<?php
$part_include = str_replace("controllers", "", __DIR__);
require_once($part_include . "traits\AdTrait.php");

class ActiveDirectoryController
{
    protected $objAdConnect;
    private $username;
    private $password;
    private $result;

    use AdTrait {
        AdTrait::__construct as private __AdTraitConstruct;
    }


    public function __construct()
    {
        self::__AdTraitConstruct();
    }

    public function setUsername($username)
    {
        $this->username = $username;
    }

    public function setPassword($password)
    {
        $this->password = $password;
    }

    public function getEntries()
    {
        $returnData = [];
        if ($this->validator() && $this->validator()['status'] === false) {
            $returnData  = $this->validator();
        } else {

            $this->objAdConnect = $this->adConnect($this->username, $this->password);

            if (isset($this->objAdConnect['count']) && $this->objAdConnect['count'] > 0) {
                http_response_code(200);
                $returnData['code'] = 200;
                $returnData['status'] = true;
                for ($i = 0; $i < $this->objAdConnect['count']; $i++) {
                    $returnData['uid'] = bin2hex($this->objAdConnect[$i]['uid'][0]);
                    $returnData['full_name'] = isset($this->objAdConnect[$i]['displayname'][0]) && !empty($this->objAdConnect[$i]['displayname'][0]) ? $this->objAdConnect[$i]['displayname'][0] : "";
                    $returnData['email'] = isset($this->objAdConnect[$i]['mail'][0]) && !empty($this->objAdConnect[$i]['mail'][0]) ? $this->objAdConnect[$i]['mail'][0] : "";
                    $returnData['telephone'] = isset($this->objAdConnect[$i]['telephonenumber'][0]) &&   !empty($this->objAdConnect[$i]['telephonenumber'][0]) ? $this->objAdConnect[$i]['telephonenumber'][0] : "";
                    $returnData['username'] = $this->username;
                    $returnData['password'] = password_hash($this->password, PASSWORD_BCRYPT);
                }
            } else {
                http_response_code(401);
                $returnData['code'] = 401;
                $returnData['status'] = false;
                $returnData['massage'] = $this->objAdConnect;
            }
        }

        $this->result = $returnData;
        return $this->result;
    }


    private function validator()
    {
        $validate = [];
        if (empty($this->username) && empty($this->password)) {
            $validate['code'] = 400;
            $validate['status'] = false;
            $validate['massage'] = "Username & Password is required";
        } else if (empty($this->username)) {
            $validate['code'] = 400;
            $validate['status'] = false;
            $validate['massage'] = "Username is required";
        } else if (empty($this->password)) {
            $validate['code'] = 400;
            $validate['status'] = false;
            $validate['massage'] = "Password is required";
        }
        http_response_code(400);
        return $validate;
    }
}
