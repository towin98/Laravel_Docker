@if ($tecnologia->pdf)
    <div class="text-red-700">
        <i class="fa-regular fa-file-pdf cursor-pointer view-pdf-technology-uploaded" data-path="{{$tecnologia->pdf}}"></i>
    </div>
@endif
