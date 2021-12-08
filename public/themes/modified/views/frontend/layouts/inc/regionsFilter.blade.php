<div class="fltr dont-break-out">
    <span class="fltr-btn fltr-all {{ request('re') != 'ntsc_u' && request('re') != 'pal' ? 'fltr-selected' : '' }}" data-value="all">
        <span>ALL</span>
    </span>
    <span class="fltr-btn fltr-us {{ request('re') == 'ntsc_u' ? 'fltr-selected' : '' }} " data-value="ntsc_u" >
        <span>
            <img class="fltr-flag" src="{{ asset('img/flags/US-moded.svg') }}" height="20"/>
        </span>
        <span class="fltr-txt">US</span>
    </span>
    <span class="fltr-btn fltr-eu {{ request('re') == 'pal' ? 'fltr-selected' : '' }}" data-value="pal">
        <span>
            <img class="fltr-flag"  src="{{ asset('img/flags/EU.svg') }}" height="20"/>
        </span>
        <span class="fltr-txt">EU</span>
    </span>
</div>
<style>
    .fltr  {
        margin-bottom: 10px;
    }
    .fltr-btn {
        padding: 8px;
        padding-bottom: 9px;
        border: none;
        color: white;
        background: #444;
        vertical-align: center;
        opacity: 40%;
        cursor: pointer;
    }
    .fltr-flag {
        margin-bottom: 2px;
        height: 20px;
    }

    .fltr-all {
        border-radius: 5px 0px 0px 5px;
    }
    .fltr-us {
        background: goldenrod;
    }
    .fltr-eu {
        border-radius: 0px 5px 5px 0px;
        background: darkblue;
    }
    .fltr-txt {
        margin-left: 5px;
    }
    .fltr-btn:hover {
        opacity: 100%;
    }
    .fltr-selected {
        opacity: 100%;
    }
    @media(max-width: 600px) {
        .fltr-txt {
            margin-left: 0px;
        }
        .fltr-flag {
            display: none;
        }
    }
</style>