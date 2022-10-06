<?php

public function updateUsers($users)
{
	foreach ($users as $user) {
		try {
			DB::beginTransaction();
			if ($user['name'] && $user['login'] && $user['email'] && $user['password'] && strlen($user['name']) >= 10)
			{
				DB::table('users')->where('id', $user['id'])->update([
					'name' => $user['name'],
					'login' => $user['login'],
					'email' => $user['email'],
					'password' => md5($user['password'])
				]);
			} else {
				 throw new Exception('Failed to update user with id: ' . $user['id']);
			}
		DB::commit();
			
		} catch (\Exception $e) {
			DB::rollBack();
			return response()->json(['status' => 'Failed', 'error'=>$e->getMessage()], 500);
		}
	}
	return Redirect::back()->with(['success', 'All users updated.']);
}

public function storeUsers($users)
{
    foreach ($users as $user) {
		DB::beginTransaction();		
		try {
			if ($user['name'] && $user['login'] && $user['email'] && $user['password'] && strlen($user['name']) >= 10)
			{
				DB::table('users')->insert([
					'name' => $user['name'],
					'login' => $user['login'],
					'email' => $user['email'],
					'password' => md5($user['password'])
				]);
			} else {
				throw new Exception('Failed to insert user with name: ' . $user['name']);
			}
		DB::commit();
        } catch (\Exception $e) {
			DB::rollBack();
			return response()->json(['status' => 'Failed', 'error'=>$e->getMessage()], 500);
        }
    }
    $this->sendEmail($users);
    return Redirect::back()->with(['success', 'All users created.']);
}

private function sendEmails($users)
{
    foreach ($users as $user) {
		$user_login = filter_var($user['login'], FILTER_SANITIZE_STRING);
        $message = 'Account has beed created. You can log in as <b>' . $user_login . '</b>';
        if ($user['email']) {
            Mail::to($user['email'])
                ->cc('support@company.com')
                ->subject('New account created')
                ->queue($message);
        }
    }
    return true;
}

?>
