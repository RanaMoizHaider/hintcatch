<?php

namespace App\Livewire\Components;

use Livewire\Component;

class Badge extends Component
{
    public $text = '';
    public $size = 'base'; // 'xs', 'sm', 'base', 'lg'
    public $variant = 'default'; // 'default', 'primary', 'secondary', 'success', 'warning', 'danger'
    public $rounded = 'rounded-full';
    public $removable = false;
    public $removeAction = null;
    public $class = '';
    public $computedClasses = '';

    public function mount(
        $text = '',
        $size = 'base',
        $variant = 'default',
        $rounded = 'rounded-full',
        $removable = false,
        $removeAction = null,
        $class = ''
    ) {
        $this->text = $text;
        $this->size = $size;
        $this->variant = $variant;
        $this->rounded = $rounded;
        $this->removable = $removable;
        $this->removeAction = $removeAction;
        $this->class = $class;
        $this->computedClasses = $this->getClasses();
    }

    public function getClasses()
    {
        $sizeClasses = [
            'xs' => 'px-2 py-0.5 text-xs font-medium',
            'sm' => 'px-2.5 py-0.5 text-xs font-medium', 
            'base' => 'px-2.5 py-1 text-sm font-medium',
            'lg' => 'px-3 py-1.5 text-sm font-medium'
        ];
        
        $variantClasses = [
            'default' => 'bg-zinc-100 dark:bg-zinc-800 text-zinc-700 dark:text-zinc-300 ring-1 ring-zinc-200 dark:ring-zinc-700',
            'primary' => 'bg-zinc-900 dark:bg-white text-white dark:text-zinc-900 ring-1 ring-zinc-900 dark:ring-white',
            'secondary' => 'bg-zinc-50 dark:bg-zinc-900/50 text-zinc-600 dark:text-zinc-400 ring-1 ring-zinc-200 dark:ring-zinc-800',
            'success' => 'bg-emerald-50 dark:bg-emerald-950/50 text-emerald-700 dark:text-emerald-400 ring-1 ring-emerald-200 dark:ring-emerald-800/50',
            'warning' => 'bg-amber-50 dark:bg-amber-950/50 text-amber-700 dark:text-amber-400 ring-1 ring-amber-200 dark:ring-amber-800/50',
            'danger' => 'bg-rose-50 dark:bg-rose-950/50 text-rose-700 dark:text-rose-400 ring-1 ring-rose-200 dark:ring-rose-800/50',
        ];
        
        return 'inline-flex items-center gap-x-1.5 ' . 
               $sizeClasses[$this->size] . ' ' . 
               $variantClasses[$this->variant] . ' ' . 
               $this->rounded . ' ' . 
               $this->class;
    }

    public function render()
    {
        return view('livewire.components.badge');
    }
}
