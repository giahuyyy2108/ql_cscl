<?PHP
require_once ("web_src/bean/UserPeer.php");

class loginAction
{
	private function isStrongPassword($password)
	{
		return strlen($password) > 8
			&& preg_match('/[A-Z]/', $password)
			&& preg_match('/[0-9]/', $password)
			&& preg_match('/[^A-Za-z0-9]/', $password);
	}

	function index()
	{
		$request = new Request;
		$util = new Util;
		unset($_SESSION["sPendingPasswordUser"], $_SESSION["sPendingPasswordToken"]);
		$_SESSION["sPassword"] = $util->createKey(9);
		//$_SESSION["sUser"] = $util->createKey(9);
		$request->setAttribute("pass", $_SESSION["sPassword"]);
		//$request->setAttribute("sUser",$_SESSION["sUser"]);
		include ("www/admin/login.htm");
		return null;
	}
	function login()
	{

		$request = new Request;
		$util = new Util;
		//$_SESSION = array();

		$username = $request->getParameter("txtUserName");
		if (!isset($_SESSION["sPassword"])) {
			return $this->index();
		}
		$pass = ($_SESSION["sPassword"] != "") ? $request->getParameter($_SESSION["sPassword"]) : "";

		$_SESSION["sPassword"] = $util->createKey(9);
		//$_SESSION["sUser"] = $util->createKey(9);
		$_SESSION["sUserName"] = "";
		$_SESSION["sUserLogin"] = false;

		$request->setAttribute("pass", $_SESSION["sPassword"]);
		//$request->setAttribute("sUser",$_SESSION["sUser"]);

		// login quá 3 lần
		//echo dirname(__FILE__);
		$userPeer = new UserPeer;
		/*$count = $userPeer->getIP();
					if($count > 3){						
						$request->setAttribute("msg","Đăng nhập quá 3 lần. Vui lòng thử lại sau 10 phút.");	
						$request->setAttribute("login-disabled","disabled");	
						$request->setAttribute("username",$username);
						include("www/admin/login.htm");
						return null;			
					}		
					*/
		if ($username != "" && $pass != "") {
			try {
				//kiem tra login
				$userPeer = new UserPeer;
				if ($user = $userPeer->getUserLogin($username, $pass)) {
					$_SESSION["sUserID"] = $user->getUser("id");
					$_SESSION["sUserName"] = $user->getUser("username");
					$_SESSION["FullName"] = $user->getUser("hoTen");
					$_SESSION["AdminType"] = $user->getUser("adminType");
					// if($user->getUser("adminType") == 1){
					// 	$_SESSION["ToTiem"] = "0 OR 1=1";
					// }
					// else{
					// 	if($user->getUser("toId") == -1){
					// 		$_SESSION["ToTiem"] = "0 OR 1=1";
					// 	}
					// 	else{
					// 		$_SESSION["ToTiem"] = $user->getUser("toId");
					// 	}
					// }

					$_SESSION["sUserLogin"] = true;
					$_SESSION['discard_after'] = time() + 10;
					$_SESSION["sToken"] = $util->createKey(9);

					$userPeer->setToken($user->getUser("id"), $_SESSION["sToken"]);

					$request->setRole($user->getUser("quyen"));

					// Cho doi mat khau yeu ngay tai trang dang nhap.
					if (!$this->isStrongPassword($pass)) {
						$_SESSION["sUserLogin"] = false;
						$_SESSION["sPendingPasswordUser"] = $username;
						$_SESSION["sPendingPasswordToken"] = $util->createKey(32);
						$request->setRole("");
						$request->setAttribute("forcePasswordChange", true);
						$request->setAttribute("passwordChangeToken", $_SESSION["sPendingPasswordToken"]);
						$request->setAttribute("username", $username);
						$request->setAttribute("msg", "Mật khẩu hiện tại chưa đủ mạnh. Vui lòng đổi mật khẩu để tiếp tục.");
						include ("www/admin/login.htm");
						return null;
					}
					unset($_SESSION["sPendingPasswordUser"], $_SESSION["sPendingPasswordToken"]);

					// chuyen den trang home
					$class = _FILE_HANDLE_ . _DEFAULT_HANDLE_ . _CLASS_HANDLE_;

					include ($class . ".php");
					$class = _DEFAULT_HANDLE_ . _CLASS_HANDLE_;
					$class_handle = new $class();
					$method = "index";
					return $class_handle->$method();
				} else {
					$userPeer = new UserPeer;

					//$userPeer->saveIP();

					//$count = $userPeer->getIP();
					$count = 0;
					if ($count > 3) {
						$request->setAttribute("msg", "Đăng nhập quá 3 lần. Vui lòng thử lại sau 10 phút.");
						$request->setAttribute("login-disabled", "disabled");
					} else {
						$request->setAttribute("msg", "Đăng nhập không thành công. Vui lòng thử lại.");
					}
					$request->setAttribute("username", $username);
					include ("www/admin/login.htm");
					return null;
				}
			} catch (Throwable $e) {
				echo $e->getMessage();
			}
		}

		//$request->setAttribute("pass",$_SESSION["sPassword"]);
		include ("www/admin/login.htm");
		return null;
	}
	function logout()
	{
		$request = new Request;
		$_SESSION["sUserID"] = "";
		$_SESSION["sUserName"] = "";
		$_SESSION["FullName"] = "";
		$_SESSION["AdminType"] = 0;
		$_SESSION["sUserLogin"] = false;
		$_SESSION["sToken"] = "";
		$_SESSION['discard_after'] = time() + 10;
		//echo(123);
		$request->setRole("");
		//$request->setAttribute("pass",$_SESSION["sPassword"]);
		//include("www/admin/login.htm");
		$_SESSION = array();
		session_destroy();
		session_unset();
		session_write_close();
		setcookie(session_name(), '', 0, '/');
		setcookie("PHPSESSID", '', 0, '/');
		session_regenerate_id(true);

		header('Location: ' . _DEFAULT_URL_ . 'login/');
		return null;
	}
	function changePass()
	{
		$request = new Request;
		$userPeer = new UserPeer;

		$user = $userPeer->getUser($_SESSION["sUserName"]);

		if ($user == false) {
			return $this->index();
		} else {
			$request->setAttribute("user", $user);
			$request->setModel("www/admin/changePass.htm");
			return true;
		}
	}

	function saveWeakPass()
	{
		$request = new Request;
		$util = new Util;
		$username = isset($_SESSION["sPendingPasswordUser"]) ? $_SESSION["sPendingPasswordUser"] : "";
		$sessionToken = isset($_SESSION["sPendingPasswordToken"]) ? $_SESSION["sPendingPasswordToken"] : "";
		$postedToken = $request->getParameter("passwordChangeToken");
		$newPass = $request->getParameter("newpass");
		$verifyPass = $request->getParameter("verifypass");

		if ($username == "" || $sessionToken == "" || !hash_equals($sessionToken, $postedToken)) {
			unset($_SESSION["sPendingPasswordUser"], $_SESSION["sPendingPasswordToken"]);
			$request->setAttribute("msg", "Phiên đổi mật khẩu đã hết hạn. Vui lòng đăng nhập lại.");
			return $this->showLoginPage($request, $util);
		}

		if ($newPass != $verifyPass || !$this->isStrongPassword($newPass)) {
			$request->setAttribute("forcePasswordChange", true);
			$request->setAttribute("passwordChangeToken", $sessionToken);
			$request->setAttribute("username", $username);
			$request->setAttribute("msg", $newPass != $verifyPass
				? "Mật khẩu xác nhận không khớp."
				: "Mật khẩu mới chưa đáp ứng đầy đủ các yêu cầu bảo mật.");
			include ("www/admin/login.htm");
			return null;
		}

		$userPeer = new UserPeer;
		$user = $userPeer->getUser($username);
		if ($user == false) {
			unset($_SESSION["sPendingPasswordUser"], $_SESSION["sPendingPasswordToken"]);
			$request->setAttribute("msg", "Không tìm thấy tài khoản. Vui lòng đăng nhập lại.");
			return $this->showLoginPage($request, $util);
		}

		$user->setUser("password", $newPass);
		$userPeer->save($user, 1);
		unset($_SESSION["sPendingPasswordUser"], $_SESSION["sPendingPasswordToken"]);
		$request->setAttribute("msg", "Đổi mật khẩu thành công. Vui lòng đăng nhập bằng mật khẩu mới.");
		$request->setAttribute("username", $username);
		return $this->showLoginPage($request, $util);
	}

	private function showLoginPage($request, $util)
	{
		$_SESSION["sPassword"] = $util->createKey(9);
		$request->setAttribute("pass", $_SESSION["sPassword"]);
		include ("www/admin/login.htm");
		return null;
	}

	function savePass()
	{
		$request = new Request;
		$userPeer = new UserPeer;

		$oldPass = $request->getParameter("oldpass");
		$newPass = $request->getParameter("newpass");
		$verifyPass = $request->getParameter("verifypass");

		$userPeer = new UserPeer;

		$user = $userPeer->getUser($_SESSION["sUserName"]);

		if ($user == false) {
			return $this->index();
		} else {
			if (md5($oldPass) != $user->getUser("password")) {
				$msg = "M&#7853;t kh&#7849;u c&#361; kh&ocirc;ng ch&iacute;nh x&aacute;c.";
				$request->setAttribute("user", $user);
				$request->setAttribute("msg", $msg);
				$request->setModel("www/admin/changePass.htm");
				return true;
			} elseif ($newPass != $verifyPass) {
				$msg = "X&aacute;c nh&#7853;n m&#7853;t kh&#7849;u m&#7899;i kh&ocirc;ng kh&#7899;p.";
				$request->setAttribute("user", $user);
				$request->setAttribute("msg", $msg);
				$request->setModel("www/admin/changePass.htm");
				return true;
			} elseif (!$this->isStrongPassword($newPass)) {
				$msg = "M&#7853;t kh&#7849;u m&#7899;i ph&#7843;i d&agrave;i h&#417;n 8 k&yacute; t&#7921;, c&oacute; ch&#7919; in hoa, ch&#7919; s&#7889; v&agrave; k&yacute; t&#7921; &#273;&#7863;c bi&#7879;t.";
				$request->setAttribute("user", $user);
				$request->setAttribute("msg", $msg);
				$request->setModel("www/admin/changePass.htm");
				return true;
			} else {
				$user->setUser("password", $newPass);
				$userPeer->save($user, 1);
				$request->setModel("www/admin/changePassSuccess.htm");
				return true;
			}
		}
	}
}
?>
