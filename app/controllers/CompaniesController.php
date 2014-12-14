<?php
use Phalcon\Tag;
class CompaniesController extends \Phalcon\Mvc\Controller
{

  public function indexAction()
  {
    $query = $this->modelsManager->createQuery("SELECT * FROM Companies");
    $companies = $query->execute();
    $company_name = "";
    foreach($companies as $company) {
      $company_name .= "<li>" . Tag::linkTo("companies/show/".$company->url, $company->name)  . "</li>";
    }
    $this->view->setVar("list_companies", $company_name);
  }
	public function showAction($url = false) 
	{
		$phql = "SELECT * FROM Companies where url LIKE :url:";
		$query = $this->modelsManager->executeQuery($phql,
			array('url'=>$url));
      
		foreach($query as $company) {
      $this_company = $company;
		}
    $this->view->setVar("company", $this_company);
    $phql = "SELECT * FROM Systems where company_id LIKE :id:";
    $query = $this->modelsManager->executeQuery($phql,
      array('id'=>$this_company->id));      
    $this_systems = "";
		foreach($query as $system) {
      $this_systems .= "<li>" . Tag::linkTo("systems/show/".$system->url, $system->name) . "</li>";
		}    
    
    $this->view->setVar("systems", $this_systems);



	}
  public function editAction($id = false)
  {
      $request = new \Phalcon\Http\Request();
      $responses = Array();
      $form_values = Array();
      
      if ($request->isPost() == true) {
        if($request->isAjax() == true) {
          $company = new Companies();
          //Store and check for errors
          $success = $company->save($request->getPost(),array('id','name','year','url','wiki_link','country'));

          if($success) {
            
            $responses['message'] = "<div class='alert alert-success'>Company saved!</div>";
          }
          else {
            $responses['message'] = "<div class='alert alert-danger'>Sorry, the following problems were generated:";
            foreach($company->getMessages() as $message) {
              $responses['message'] .= "<br/>" . $message->getMessage(0,"<br/>");
            }
            $responses['message'] .= "</div>";
          }
          echo json_encode($responses);
          
          $this->view->disable();
        }// if isAjax
      }// if isPost
      else {
        if($id) {

          //$this->view->disable();
          $query = $this->modelsManager->executeQuery("SELECT * FROM Companies where id LIKE :id:",
            array('id'=>$id));  
          foreach($query as $company) {
            $this->view->setVar("company", $company);
          }
        }
        else {
          $new_company =  new stdClass();
          $new_company->id = 0;
          $new_company->name = "";
          $new_company->country = "";
          $new_company->wiki_link = "";
          $new_company->url = "";
          $this->view->setVar("company", $new_company);
        }
      }
  }	

}
?>