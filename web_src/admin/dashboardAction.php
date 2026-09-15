<?PHP
class dashboardAction {
	
	public function index(){
		$request = new Request;		
		$request->setModel("www/admin/dashboard/viewDashboard.htm");
		return true;
	}
}
?>