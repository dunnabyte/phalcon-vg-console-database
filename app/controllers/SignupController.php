<?php

class SignupController extends \Phalcon\Mvc\Controller
{
    
    public function indexAction()
    {
		echo "test";
    }
    public function registerAction()
    {
      $request = new \Phalcon\Http\Request();
      $responses = Array();
    
      if ($request->isPost() == true) {
        if($request->isAjax() == true) {
          $signup = new NewsletterSignups();
          //Store and check for errors
          $success = $signup->save($this->request->getPost(),array('name','email','test'));
          
          if($success) {
            
            $responses['content'] = "Thanks for signing up";
          }
          else {
            $responses['content'] = "<br>Sorry, the following problems were generated:";
            foreach($signup->getMessages() as $message) {
              $responses['content'] .= "<br/>" . $message->getMessage(0,"<br/>");
            }
          }
          echo json_encode($responses);
          
          $this->view->disable();
        }// if isAjax
      }// if isPost
    }	//function registerAction

}
?>