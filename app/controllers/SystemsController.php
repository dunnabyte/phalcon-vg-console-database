<?php
use Phalcon\Tag;
class SystemsController extends \Phalcon\Mvc\Controller
{

  public function indexAction()
  {
    $query = $this->modelsManager->createQuery("SELECT Systems.*,Companies.* FROM Systems left join Companies on Systems.company_id = Companies.id");
    $systems = $query->execute();
    $system_name = "<thead>
                  <tr><th>Console System Name</th>
                  <th>Release Year</th>
                  <th>Company</th>
                  </tr></thead>";
    foreach($systems as $system) {
      
      $system_name .= "<tr>
                      <td>" . Tag::linkTo("systems/show/".$system->systems->url, $system->systems->name)  . "</td>
                      <td>". $system->systems->year . "</td>
                      <td>" . Tag::linkTo("companies/show/".$system->companies->url, $system->companies->name)  . "</td>
                      </tr>";
                      
    }
    $this->view->setVar("list_systems", $system_name);
  }
	public function showAction($url = false) 
	{
		$phql = "SELECT Systems.*, Companies.*, ListMediaFormats.* FROM Systems 
            left join Companies on Systems.company_id = Companies.id 
            left join ListMediaFormats on Systems.media_format_id = ListMediaFormats.id  
            where Systems.url LIKE :url:";
		$query = $this->modelsManager->executeQuery($phql,
			array('url'=>$url));
		foreach($query as $this_system) {
      if($this_system->systems->ram_bytes > 1048576) {
        $this_system->systems->ram_formatted = ceil($this_system->systems->ram_bytes / 1048576) . " Megabytes";
      }
      elseif($this_system->systems->ram_bytes > 1024) {
        $this_system->systems->ram_formatted = ceil($this_system->systems->ram_bytes / 1024) . " Kilobytes";
      }
      else {
        $this_system->systems->ram_formatted = ceil($this_system->systems->ram_bytes) . " Bytes";
      }
      
			$this->view->setVar("console_system", $this_system);
      
      //print_r($system);
      //exit;
      //$this->view->disable();
			
		}

	}
  public function editAction($id = false)
  {
      $request = new \Phalcon\Http\Request();
      $responses = Array();
      $form_values = Array();

		$query = $this->modelsManager->executeQuery("SELECT * from ListGenerations order by id");
    $dropdown_generations[""] = "-Select-";
		foreach($query as $this_row) {       
      $dropdown_generations[$this_row->id] = $this_row->name;
    }
    
		$query = $this->modelsManager->executeQuery("SELECT * from Companies order by name");
    $dropdown_companies[""] = "-Select-";
		foreach($query as $this_row) {       
      $dropdown_companies[$this_row->id] = $this_row->name;
    }    
    
		$query = $this->modelsManager->executeQuery("SELECT * from ListMediaFormats order by name");
    $dropdown_media_formats[""] = "-Select-";
		foreach($query as $this_row) {       
      $dropdown_media_formats[$this_row->id] = $this_row->name;
    }         
    
    if ($request->isPost() == true) {
      if($request->isAjax() == true) {
        $system = new Systems();
        //Store and check for errors
        $success = $system->save($request->getPost(),array('id','name','year','url','wiki_link','ram_bytes','num_games','company_id','generation_id','media_format_id','units_sold','cpu'));

        if($success) {
          
          $responses['message'] = "<div class='alert alert-success'>System saved!</div>";
        }
        else {
          $responses['message'] = "<div class='alert alert-danger'>Sorry, the following problems were generated:";
          foreach($system->getMessages() as $message) {
            $responses['message'] .= "<br/>" . $message->getMessage(0,"<br/>");
          }
          $responses['message'] .= "</div>";
        }
        echo json_encode($responses);
        
        $this->view->disable();
      }// if isAjax
    }// if isPost
    else {
      
      
      $this->view->setVar("dropdown_generations",$dropdown_generations);
      $this->view->setVar("dropdown_companies",$dropdown_companies);
      $this->view->setVar("dropdown_media_formats",$dropdown_media_formats);
      
      
      if($id) {
        $this->view->setVar("page_title","Edit Console System");
        //$this->view->disable();
        $query = $this->modelsManager->executeQuery("SELECT * FROM Systems where id LIKE :id:", array('id'=>$id));  
        foreach($query as $this_system) {
          $this->view->setVar("link_return",Tag::linkTo("systems/show/" . $this_system->url, "View Company"));        
          $this->view->setVar("system", $this_system);
        }
      }
      else {
        $this->view->setVar("page_title","Add New Console System");

        $this->view->setVar("link_return",Tag::linkTo("systems/", "Return to List of Console Systems"));       
        $this_system = new stdClass();
        $this_system->url="";
        $this_system->name="";
        $this_system->id="";
        $this_system->company_id="";
        $this_system->generation_id="";
        $this_system->ram_bytes="";
        $this_system->wiki_link="";
        $this_system->units_sold="";
        $this_system->year="";
        $this_system->cpu="";
        $this_system->num_games="";
        $this_system->media_format_id="";
        $this->view->setVar("system", $this_system);
        
      }
    }
  }	

}
?>