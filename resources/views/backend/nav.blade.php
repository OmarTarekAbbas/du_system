<ul class="nav menu">
    <li class="active"><a href="{{ url('admin') }}"><span class="glyphicon glyphicon-dashboard"></span> Dashboard</a></li>
    <li class="parent">
        <a href="#sub-item-1"  data-toggle="collapse">
            <span class="glyphicon glyphicon-list"></span> Messages <span data-toggle="collapse" href="#sub-item-1" class="icon pull-right"><em class="glyphicon glyphicon-s glyphicon-plus"></em></span>
        </a>
        <ul class="children collapse" id="sub-item-1">
            <li>
                <a class="" href="{{url('admin/mt/create')}}">
                    <span class="glyphicon glyphicon-share-alt"></span> Send Message
                </a>
            </li>
            <li>
                <a class="" href="{{url('admin/mt')}}">
                    <span class="glyphicon glyphicon-share-alt"></span> List Messages
                </a>
            </li>
        </ul>
    </li>
    <li class="parent">
        <a href="#sub-item-2"  data-toggle="collapse">
            <span class="glyphicon glyphicon-list-alt"></span> Services <span data-toggle="collapse" href="#sub-item-2" class="icon pull-right"><em class="glyphicon glyphicon-s glyphicon-plus"></em></span>
        </a>
        <ul class="children collapse" id="sub-item-2">
            @if(Auth::user()->admin == true)
            <li>
                <a class="" href="{{url('admin/services/create')}}">
                    <span class="glyphicon glyphicon-plus-sign"></span> Add Service
                </a>
            </li>
            @endif
            <li>
                <a class="" href="{{url('admin/services')}}">
                    <span class="glyphicon glyphicon-list-alt"></span> List Services
                </a>
            </li>
        </ul>
    </li>
    @if(Auth::user()->admin == true)
    <li class="parent">
        <a href="#sub-item-3"  data-toggle="collapse">
            <span class="glyphicon glyphicon-list-alt"></span> Country <span data-toggle="collapse" href="#sub-item-2" class="icon pull-right"><em class="glyphicon glyphicon-s glyphicon-plus"></em></span>
        </a>
        <ul class="children collapse" id="sub-item-3">

            <li>
                <a class="" href="{{url('admin/country/create')}}">
                    <span class="glyphicon glyphicon-plus-sign"></span> Add Country
                </a>
            </li>

            <li>
                <a class="" href="{{url('admin/country')}}">
                    <span class="glyphicon glyphicon-list-alt"></span> List Countries
                </a>
            </li>
        </ul>
    </li>
    <li class="parent">
        <a href="#sub-item-4"  data-toggle="collapse">
            <span class="glyphicon glyphicon-list-alt"></span> Operator <span data-toggle="collapse" href="#sub-item-2" class="icon pull-right"><em class="glyphicon glyphicon-s glyphicon-plus"></em></span>
        </a>
        <ul class="children collapse" id="sub-item-4">

            <li>
                <a class="" href="{{url('admin/operator/create')}}">
                    <span class="glyphicon glyphicon-plus-sign"></span> Add Operator
                </a>
            </li>

            <li>
                <a class="" href="{{url('admin/operator')}}">
                    <span class="glyphicon glyphicon-list-alt"></span> List Operators
                </a>
            </li>
        </ul>
    </li>
    <li id="subc"><a href="{{ route('admin.subscribers.index') }}"><span class="glyphicon glyphicon-random"></span> Subscribers</a></li>
    <li id="unsubc"><a href="{{ route('admin.unsubscribers.index') }}"><span class="glyphicon glyphicon-random"></span> UnSubscribers</a></li>
    <li id="charge"><a href="{{ route('admin.charges.index') }}"><span class="glyphicon glyphicon-random"></span> Charges</a></li>
    <li id="faild"><a href="{{ route('admin.faild.charge.get') }}"><span class="glyphicon glyphicon-random"></span> Faild Today Charges</a></li>
    <li id="activations"><a href="{{ route('admin.activations.index') }}"><span class="glyphicon glyphicon-random"></span> Activations</a></li>
    <li id="logmessage"><a href="{{ url('admin/logmessage') }}"><span class="glyphicon glyphicon-random"></span> Log Messages</a></li>
    <li id="excel"><a href="{{ url('admin/subscribe/excel') }}"><span class="glyphicon glyphicon-list"></span> Subscribe Excel </a></li>
    @endif
    <li role="presentation" class="divider"></li>
    <li><a href="{{ url('service') }}"><span class="glyphicon glyphicon-random"></span> Change service</a></li>
    @if(Auth::user()->admin == true)
        <li><a href="{{ url('admin/user/create') }}"><span class="glyphicon glyphicon-user"></span> Add User</a></li>
        <li><a href="{{ url('admin/user') }}"><span class="glyphicon glyphicon-list"></span> Users list</a></li>
    @endif
</ul>
