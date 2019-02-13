<?php

class dl_filebox_com extends Download
{

    public function CheckAcc($cookie)
    {
        $data = $this->lib->curl("http://www.filebox.com/?op=my_account", "lang=english;{$cookie}", "");
        if (stristr($data, 'Premium account expire:')) {
            return array(true, "Until " . $this->lib->cut_str($data, '<TR><TD>Premium account expire:</TD><TD><b>', '</b></TD><TD>'));
        } else if (stristr($data, 'New password') && !stristr($data, 'Premium account expire')) {
            return array(false, "accfree");
        } else {
            return array(false, "accinvalid");
        }

    }

    public function Login($user, $pass)
    {
        $data = $this->lib->curl("http://www.filebox.com/", "lang=english", "login={$user}&password={$pass}&op=login&redirect=");
        $cookie = "lang=english;{$this->lib->GetCookies($data)}";

        return array(true, $cookie);
    }

    public function Leech($url)
    {
        $data = $this->lib->curl($url, $this->lib->cookie, "");
        if (stristr($data, '<h2>This file is no longer available</h2>')) {
            $this->error("dead", true, false, 2);
        } elseif (!$this->isredirect($data)) {
            $post = $this->parseForm($this->lib->cut_str($data, '<Form style=\'display:inline-block\'', '</form>'));
            $data = $this->lib->curl($url, $this->lib->cookie, $post);
            if (preg_match('@https?:\/\/www(\d+\.)?filebox\.com(:\d+)?\/d\/[a-z0-9]+\/[^/|\"|\'|<|>|\r|\n|\t]+@i', $this->lib->cut_str($data, 'onclick="document.location=\'', '\'" value=\'Download\' /></center>'), $giay)) {
                return trim($giay[0]);
            }

        } else {
            return trim($this->redirect);
        }

        return false;
    }

}

/*
 * Open Source Project
 * New Vinaget by LTT
 * Version: 3.3 LTS
 * Filebox.com Download Plugin
 * Date: 01.09.2018
 */
