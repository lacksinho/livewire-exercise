<?php

namespace App\Http\Livewire;

use App\Http\Requests\CategoryRequest;
use App\Models\Category;
use Illuminate\Support\Collection;
use Illuminate\Support\Str;
use Illuminate\View\View;
use Livewire\Component;
use Livewire\WithPagination;

class CategoriesList extends Component
{
    use WithPagination;

    public Category $category;

    public Collection $categories;

    public bool $showModal = false;

    public array $active = [];

    public int $editedCategoryId = 0;

    protected $listeners = ['delete'];

    public function render(): View
    {
        $cats = Category::orderBy('position')->paginate(10);
        $links = $cats->links();
        $this->categories = collect($cats->items());

        $this->active = $this->categories->mapWithKeys(
            fn(Category $item) => [$item['id'] => (bool)$item['is_active']]
        )->toArray();

        return view('livewire.categories-list', [
            'links' => $links
        ]);
    }

    protected function rules(): array
    {
        return (new CategoryRequest())->rules();
    }

    public function openModal()
    {
        $this->showModal = true;

        $this->category = new Category();
    }

    public function updatedCategoryName()
    {
        $this->category->slug = Str::slug($this->category->name);
    }

    public function toggleIsActive($categoryId)
    {
        Category::where('id', $categoryId)->update([
            'is_active' => $this->active[$categoryId],
        ]);
    }

    public function save()
    {
        $this->validate();


        if ($this->editedCategoryId === 0) {
            $this->category->position = Category::max('position') + 1;
        }

        $this->category->save();

        $this->reset('showModal', 'editedCategoryId');
    }

    public function updateOrder($list)
    {
        foreach ($list as $item) {
            $cat = $this->categories->firstWhere('id', $item['value']);

            if ($cat['position'] != $item['order']) {
                Category::where('id', $item['value'])->update(['position' => $item['order']]);
            }
        }
    }

    public function editCategory($categoryId)
    {
        $this->editedCategoryId = $categoryId;

        $this->category = Category::find($categoryId);
    }

    public function cancelCategoryEdit()
    {
        $this->reset('editedCategoryId');
    }

    public function deleteConfirm($method, $id = null)
    {
        $this->dispatchBrowserEvent('swal:confirm', [
            'type' => 'warning',
            'title' => 'Are you sure?',
            'text' => '',
            'id' => $id,
            'method' => $method,
        ]);
    }

    public function delete($id)
    {
        Category::findOrFail($id)->delete();
    }
}
