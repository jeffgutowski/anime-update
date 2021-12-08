@extends(Theme::getLayout())
@include('frontend.cart.subheader')

@section('content')

    <div class="panels-title border-bottom flex-center-space">
        <div class="full-width">
            <i class="fa fa-shopping-cart" aria-hidden="true"></i> {{trans('cart.cart')}}
          <a href="{{ route('checkout.cart.clear') }}" class="btn btn-danger float-right">{{trans('cart.clear_cart')}}</a>
        </div>
    </div>
    @if (! Cart::count())
      <p class="alert">{{trans('cart.empty_cart')}}</p>
    @endif

@php($cart = App\Libraries\Cart::sellerContent())
@php($sellers = [])
    @foreach($cart as $seller_id => $seller)
  <section class="section-content bg padding-y border-top cart-section">

    <div class="container">

      <div class="row">
        <main class="col-sm-9">
            <div class="card">
              <div id="content-desktop">
              <table class="table table-hover shopping-cart-wrap">
                <thead class="text-muted">
                <tr>
                  <th></th>
                  <th scope="col">{{trans('cart.headers.product')}}</th>
                  <th scope="col">{{trans('cart.headers.platform')}}</th>
                  <th scope="col">{{trans('cart.headers.condition')}}</th>
                  <th scope="col" style="width:100px">{{trans('cart.headers.price')}}</th>
                  <th scope="col" class="text-right"></th>
                </tr>
                </thead>
                <tbody>
                @foreach($seller->items as $item)
                  <tr>
                    <td class="cart-item-image">
                      <img src="{{$item->model->product->cover_us}}">
                    </td>
                    <td>
                      <figure class="media">
                        <figcaption class="media-body">
                          <div class="font-weight-bold">{{$item->name}}</div>
                        </figcaption>
                      </figure>
                    </td>
                    <td>
                      <figure class="media">
                        <figcaption class="media-body">

                          <div>
                            <span style="background-color: {{$item->model->product->platform->color}}; color: {{$item->model->product->platform->text_color}}; border-radius: 5px; padding:3px;">{{$item->model->product->platform->name}}</span>
                          </div>
                        </figcaption>
                      </figure>
                    </td>
                    <td>
                      <div>
                        {{$item->model->getConditionStringAttribute()}}
                      </div>
                    </td>
                    <td>
                      <div class="price-wrap">
                        <var class="price">{{ "".money($item->price, Config::get('settings.currency')) }}</var>
                      </div>
                    </td>
                    <td class="text-right">
                      <a href="{{ route('checkout.cart.remove', $item->rowId) }}" class="btn btn-outline-danger"><i class="fa fa-times"></i> </a>
                    </td>
                  </tr>
                @endforeach
                </tbody>
              </table>
              </div>
              <div id="content-mobile">
                <div class="card">
                    <table class="table table-hover shopping-cart-wrap">
                      <thead class="text-muted">
                      <tr>
                        <th></th>
                      </tr>
                      </thead>

                      <tbody>
                      @foreach($seller->items as $item)
                        <tr>
                          <td class="mobile-cart-item-image">
                            <div>
                              <img src="{{$item->model->product->cover_us}}">
                            </div>
                          </td>
                          <td width="100%">
                            <a href="{{ route('checkout.cart.remove', $item->rowId) }}" class="float-right cart-remove-link"><i class="fa fa-times"></i> </a>
                                <div class="font-weight-bold">{{$item->name}} </div>
                            <div>
                              <span class="mobile-platform-header" style="background-color: {{$item->model->product->platform->color}}; color: {{$item->model->product->platform->text_color}};">{{$item->model->product->platform->name}}</span>
                            </div>
                            <div>
                              {{trans('cart.headers.condition')}}: {{$item->model->getConditionStringAttribute()}}
                            </div>
                            <div class="price-wrap float-right align-bottom">
                              <var class="price" >{{ "".money($item->price, Config::get('settings.currency')) }}</var>
                            </div>
                          </td>
                        </tr>
                      @endforeach
                      </tbody>
                    </table>
              </div>
            </div>
          </div>
        </main>
        <aside class="col-sm-3">
          {{trans('cart.sold_by')}} <a href="/user/{{$seller->items->{0}->model->user->name}}">{{$seller->items->{0}->model->user->name}}</a>
          <dl class="dlist-align h4">
            <dt>Total:</dt>
            <dd class="text-right"><strong>{{ money($seller->subtotal, Config::get('settings.currency')) }}</strong></dd>
          </dl>
          <hr>
          @php($sellers[] =  (int) $seller_id)
          <div id="paypal-button-{{$seller_id}}" class="pointer-events-none"></div>
        </aside>
      </div>
    </div>
  </section>
  <br/>
@endforeach
    <script src="https://www.paypalobjects.com/api/checkout.js"></script>
    <script>
      var sellers = JSON.parse("{{ json_encode($sellers) }}");
      for (i = 0; i < sellers.length; i++) {
        (function () {
          paypal.Button.render({
            style: {
              tagline: false
            },
            env: 'sandbox', // Or 'sandbox',
            payment: function (data, actions) {
              // You'll configure this in the following steps.
            },
            onAuthorize: function (data, actions) {
              return actions.redirect();
            },
            onCancel: function (data, actions) {
              return actions.redirect();
            },
            onError: function (error) {
              // You will want to handle this differently
              return alert(error);
            }
          }, '#paypal-button-' + sellers[i]);
        })();
      }
    </script>
<style>
#content-mobile {display: none;}

@media screen and (max-width: 768px) {

  #content-desktop {display: none;}
  #content-mobile {display: block;}

}
</style>
@stop
