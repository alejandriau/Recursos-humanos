@props(['title' => '', 'userName' => null])

<div class="alert alert-secondary" role="alert">
    <div class="row justify-content-start align-items-center">
        <div class="col-9">
            <b>{{ $title }}</b>
        </div>
        @if($userName)
        <div class="col-3 text-primary d-flex justify-content-end">
            <i class="fa-solid fa-user fs-4"></i>&nbsp;{{ $userName }}
        </div>
        @endif
    </div>
</div>