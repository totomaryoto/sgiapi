<?php
class LanguageChanger
  {
      function initialize() {
          $ci =& get_instance();
          $ci->load->helper('language');
          $siteLang = $ci->session->userdata('site_lang');
          $ci->lang->load('app',$siteLang);
      }
  }