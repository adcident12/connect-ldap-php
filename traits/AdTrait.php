<?php

trait AdTrait
{
    private  $adCon;
    private $adServer;
    private $dn;
    private $result;

    private function __construct()
    {
        $part = str_replace("traits", "", __DIR__);
        require_once($part . "vendor/autoload.php");

        $dotenv = Dotenv\Dotenv::createImmutable($part);
        $dotenv->load();

        $this->adServer = $_ENV['LDAP_SERVER'];
        $this->dn = $_ENV['LDAP_BASE_DN'];
    }

    private function  adConnect($username, $password)
    {
        $this->adCon = ldap_connect($this->adServer);

        if ($this->adCon) {
            $this->setAdOption($this->adCon);
            if (!$this->adBind($this->adCon, $username, $password)) {
                $error_no = ldap_errno($this->adCon);
                $this->result = ldap_err2str($error_no);
            } else {
                $this->result = $this->searchUser($this->adCon, $username);
            }

            $this->unBindAd($this->adCon);
        } else {
            $this->result = "Connection fail";
        }

        return $this->result;
    }

    private function setAdOption($conn)
    {
        ldap_set_option($conn, LDAP_OPT_PROTOCOL_VERSION, 3);
    }

    private function adBind($conn, $username, $password)
    {
        return @ldap_bind($conn, "uid=" . $username . ",ou=Users,"  . $this->dn, $password);
    }

    private function searchUser($conn, $username)
    {
        $filter = "uid=" . $username;
        $result = @ldap_search($conn, "uid=" . $username . ",ou=Users,"  . $this->dn, $filter);

        if (!$result) {
            $error_no = ldap_errno($this->adCon);
            return ldap_err2str($error_no);
        }

        return ldap_get_entries($conn, $result);
    }

    private function unBindAd($conn)
    {
        ldap_unbind($conn);
    }
}
