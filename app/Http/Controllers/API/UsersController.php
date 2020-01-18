<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class UsersController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('auth:api');
    }
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        //
        $this->authorize('isAdmin');
        return User::latest()->paginate(5);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        //
        $this->authorize('isAdmin');
        $this->validate($request, [
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users'],
            'password' => ['required', 'string', 'min:8'],
        ]);
        return User::create([
            'name' => $request['name'],
            'email' => $request['email'],
            'type' => $request['type'],
            'bio' => $request['bio'],
            'photo' => $request['photo'],
            'password' => Hash::make($request['password']),
        ]);
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        //

        $user = User::findorfail($id);
        $this->validate($request, [
            'name' => ['required', 'string', 'max:255'],
            'password' => ['sometimes', 'required', 'string', 'min:8'],
            'email' => 'required|string|max:30|unique:users,email,' . $user->id,
        ]);



        $user->update($request->all());

        return ['message' => 'User Updated successfully'];
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
        $this->authorize('isAdmin');
        $user = User::findorfail($id);

        $user->delete();

        return ['message' => 'User Deleted successfully'];
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function profile()
    {
        //
        return auth('api')->user();
    }
    public function search(Request $request)
    {
        //
        if ($search = $request->q) {
            $users = User::where(function ($query) use ($search) {
                $query->where('name', 'LIKE', "%$search%")->orWhere('email', 'LIKE', "%$search%");
            })->paginate(5);
        } else {
            $users = User::latest()->paginate(5);
        }

        return $users;

        // return $request->q;
    }

    public function updateprofile(Request $request)
    {
        //
        $user = auth('api')->user();

        $this->validate($request, [
            'name' => ['required', 'string', 'max:255'],
            'password' => ['sometimes', 'string', 'min:8'],
            'email' => 'required|string|unique:users,email,' . $user->id,
        ]);

        $current = $user->photo;
        $ext = explode('/', explode(':', substr($request->photo, 0, strpos($request->photo, ";")))[1])[1];

        if ($request->photo != $current) {
            $name = time() . "-" . uniqid() . "." . $ext;

            \Image::make($request->photo)->save(public_path('img/profile/') . $name);

            $request->merge(['photo' => $name]);

            $userimage = public_path('img/profile/') . $current;

            if (file_exists($userimage)) {
                unlink($userimage);
            }
        }

        if (!empty($request->password)) {

            $request->merge(['password' => Hash::make($request->password)]);
        }

        $user->update($request->all());

        return ["Message" => "Profile update Successful"];
    }
}
