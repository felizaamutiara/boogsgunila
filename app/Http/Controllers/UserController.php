<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Kelas;
use App\Models\UserModel;
class UserController extends Controller
{
    public $userModel;
    public $kelasModel;

    public function __construct(){
        $this->userModel = new UserModel();
        $this->kelasModel = new Kelas();
    }

    public function index(){
        $data = [
            'title' => 'List User',
            'users' => $this->userModel->getUser()
        ];
        return view('list_user', $data);
    }
    public function create(){
        $kelasModel = new Kelas();
        $Kelas = $kelasModel->getKelas();
        $data = [
            'title' => 'Create User',
            'kelas' => $Kelas
        ];

        return view('create_user', $data);
    }

    public function store(Request $request){
        $request->validate([
            'nama' => 'required|string|max:150',
            'nim' => 'required|string|max:20|unique:users,nim',
            'kelas_id' => 'required|exists:kelas,id'
        ]);

        $this->userModel->create([
            'nama' => $request->input('nama'),
            'nim' => $request->input('nim'),
            'kelas_id' => $request->input('kelas_id')
        ]);

        return redirect()->to('/user')->with('success', 'User berhasil ditambahkan!');
    }

    public function edit($id)
    {
        $user = $this->userModel->findUser($id);
        $kelas = $this->kelasModel->getKelas();
        
        $data = [
            'title' => 'Edit User',
            'user' => $user,
            'kelas' => $kelas
        ];

        return view('edit_user', $data);
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'nama' => 'required|string|max:150',
            'nim' => 'required|string|max:20|unique:users,nim,' . $id,
            'kelas_id' => 'required|exists:kelas,id'
        ]);

        try {
            $this->userModel->updateUser($id, [
                'nama' => $request->input('nama'),
                'nim' => $request->input('nim'),
                'kelas_id' => $request->input('kelas_id')
            ]);

            return redirect()->to('/user')->with('success', 'Data user berhasil diperbarui!');
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Gagal memperbarui data user: ' . $e->getMessage());
        }
    }

    public function destroy($id)
    {
        try {
            $this->userModel->deleteUser($id);
            return redirect()->to('/user')->with('success', 'User berhasil dihapus!');
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Gagal menghapus user: ' . $e->getMessage());
        }
    }
}