<?php

namespace App\Livewire\Admin;

use App\Models\User;
use Flux\Flux;
use Illuminate\Support\Facades\Hash;
use Livewire\Attributes\Layout;
use Livewire\Component;

class Users extends Component
{
    public $search = '';

    public ?User $user = null;

    public $name = '';

    public $email = '';

    public $password = '';

    public $editing = false;

    public function create()
    {
        $this->reset(['name', 'email', 'password', 'editing', 'user']);
        $this->dispatch('modal-show', name: 'user-modal');
    }

    public function edit(User $user)
    {
        $this->user = $user;
        $this->name = $user->name;
        $this->email = $user->email;
        $this->password = '';
        $this->editing = true;
        $this->dispatch('modal-show', name: 'user-modal');
    }

    public function save()
    {
        $rules = [
            'name' => 'required|min:3|max:255',
            'email' => 'required|email|max:255|unique:users,email'.($this->editing ? ','.$this->user->id : ''),
            'password' => $this->editing ? 'nullable|min:8' : 'required|min:8',
        ];

        $this->validate($rules);

        $data = [
            'name' => $this->name,
            'email' => $this->email,
        ];

        if ($this->password) {
            $data['password'] = Hash::make($this->password);
        }

        if ($this->editing) {
            $this->user->update($data);
            Flux::toast(__('Benutzer wurde aktualisiert.'));
        } else {
            User::create($data);
            Flux::toast(__('Benutzer wurde erstellt.'));
        }

        $this->dispatch('modal-close', name: 'user-modal');
        $this->reset(['name', 'email', 'password', 'editing', 'user']);
    }

    public function delete(User $user)
    {
        $this->user = $user;
        $this->dispatch('modal-show', name: 'delete-confirmation');
    }

    public function confirmDelete()
    {
        if ($this->user->id === auth()->id()) {
            Flux::toast(__('Sie können sich nicht selbst löschen.'), variant: 'danger');
            $this->dispatch('modal-close', name: 'delete-confirmation');

            return;
        }

        $this->user->delete();
        Flux::toast(__('Benutzer wurde gelöscht.'));

        $this->dispatch('modal-close', name: 'delete-confirmation');
        $this->reset(['user']);
    }

    public function users()
    {
        return User::query()
            ->when($this->search, fn ($query) => $query->where('name', 'like', '%'.$this->search.'%')
                ->orWhere('email', 'like', '%'.$this->search.'%'))
            ->latest()
            ->get();
    }

    #[Layout('layouts.app')]
    public function render()
    {
        return view('livewire.admin.users');
    }
}
