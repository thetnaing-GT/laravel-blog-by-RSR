@extends("layouts.app")

@section("content")
    <div class="container">

        @if ($errors->any())
            <div class="alert alert-warning">
                <ol>
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ol>
            </div>
        
        @endif
        <form action="{{ url('/articles') }}" method="POST" enctype="multipart/form-data">
            @csrf
            <div class="mb-3">
                <label>Title</label>
                <input type="text" name="title" class="form-control">
            </div>
            
            <div class="mb-3">
                <label>Image</label>
                <input type="file" name="image" class="form-control">
            </div>


            <div class="mb-3">
                <label>Body</label>
                <textarea name="body" class="form-control"></textarea>
            </div>
            
            <div class="mb-3">
                <label>Category</label>
                <select name="category_id" class="form-select mb-2">
                    <option value="">-- Select Existing Category --</option>
                    @foreach ($categories as $category)
                        <option value="{{ $category->id }}" {{ old('category_id') == $category->id ? 'selected' : '' }}>
                            {{ $category->name }}
                        </option>
                    @endforeach
                </select>
                <div class="form-text">IF you do not find, add your new category</div>
                <input type="text" name="new_category" class="form-control mt-2" placeholder="Add New Category" value="{{ old('new_category') }}">
            </div>


            <div class="mb-3">
        <label>Tags</label>
            <select name="tags[]" class="form-select" multiple>
                @foreach ($tags as $tag)
                    <option value="{{ $tag->id }}">{{ $tag->name }}</option>
                @endforeach
            </select>
    <div class="form-text">Hold Ctrl (Windows) or Command (Mac) to select multiple tags</div>
</div>

            <input type="submit" class="btn btn-primary" value="Add Article">
        </form>
    </div>
    
@endsection