@extends Template1

@section main
    <section class="app" style="border: 1px solid red;">
        <h1>{{ $name; }}</h1>
    </section>

    @if(true):
        <h1>test</h1>
    @else:
        do somthing
    @endif


    @component Example1(
        'name' => 'Ingo2',
        'content' => 'This is the content...'
    )

test 2

    <h1>{{ $name; }}</h1>

@section scripts
    <p>Test</p>