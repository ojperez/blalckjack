<!doctype html>
<html lang="{{ app()->getLocale() }}">
    <head>
        <meta charset="utf-8">
        <meta http-equiv="X-UA-Compatible" content="IE=edge">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <title>{{ env('APP_NAME')  }}</title>        
        <link href="https://fonts.googleapis.com/css?family=Raleway" rel="stylesheet" type="text/css"> 
        <link href="{{ URL::asset('css/style.css') }}" rel="stylesheet" type="text/css">
    </head>
    <body>
        <div class="flex-center position-ref full-height">
            <div class="content">
                <div class="title m-b-md">
                    {{ env('APP_NAME')  }}
                </div>
                <div class="counts text-center">
                    Hands Total: {{ $handsTotal }}
                    <br>
                    Hands Won: {{ $handsWon }}
                </div>
                @if ($wonGame != false)
                <div class="alert alert-info">
                    <h1>{{ $wonGame }}</h1>
                </div>
                @endif
                @if ($wonHand != false)
                <div class="alert @if ($wonHand != 'player') alert-info @else alert-success @endif">
                    @if ($wonHand != 'player')
                    The dealer won the hand.
                    @else
                    Congratulations! You won the hand!
                    @endif
                </div>
                @endif
                <div class="cardsTable">
                    
                
                <div class="dealerCards">
                    @if( ! empty($dealerHand))
                        @foreach($dealerHand as $card)
                            @if ($card->status == 'hidden')
                                <span class='card card-blue'>
                            @else                
                            @if ((($card->suit == 'Diamonds') || ($card->suit == 'Hearts')))
                                <span class='card card-red'>
                            @else
                                <span class='card card-black'>
                            @endif
                    @endif
                            {!! $card->toHTML() !!}
                            </span>
                        @endforeach
                    @endif
                </div>



                <div class="playerCards">
                    @if( ! empty($playerHand))
                        @foreach($playerHand as $card)               
                            @if ((($card->suit == 'Diamonds') || ($card->suit == 'Hearts')))
                                <span class='card card-red'>
                            @else
                                <span class='card card-black'>
                            @endif
                
                             {!! $card->toHTML() !!}
                            </span>
                        @endforeach
                    @endif
                </div>
                
                </div>
                

                <div class="links">
                    @foreach ($actions as $route => $label)
                        <a class="actions" href="{{ action('BlackjackGame@'.$route) }}">{{ $label }}</a>
                    @endforeach
                </div>
                <footer>
                    <small>By OJ Perez</small>                    
                </footer>
            </div>
        </div>
    </body>
</html>
