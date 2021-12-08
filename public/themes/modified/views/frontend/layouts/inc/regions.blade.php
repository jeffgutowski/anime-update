<div class="wrapper">
    <a href="/region/ntsc_u">
        <button class="btn region-btn region-btn-us">
            <span class="flag-container">
                <img src="{{ asset('img/flags/US.svg') }}" height="20"/>
            </span>
            <span class="region-btn-name">US</span>
        </button>
    </a>
    <a href="/region/pal">
        <button class="btn region-btn region-btn-eu">
            <span class="flag-container">
                <img src="{{ asset('img/flags/EU.svg') }}" height="20"/>
            </span>
            <span class="region-btn-name">EU</span>
        </button>
    </a>
    {{--
    <a href="/region/ntsc_j">
        <button class="btn region-btn region-btn-jp">
            <span class="flag-container">
                <img src="{{ asset('img/flags/JP.svg') }}" height="20"/>
            </span>
            <span class="region-btn-name">JP</span>
        </button>
    </a>
    --}}
    <a href="/region/pa">
        <button class="btn region-btn region-btn-pa">
            <span class="flag-container">
                <img src="{{ asset('img/flags/Play-Asia.png') }}" height="20"/>
            </span>
            <span class="region-btn-name region-btn-playasia">playasia</span>
        </button>
    </a>
</div>
<style>
    .region-btn-us {
        opacity: {{ session("region.code") == "ntsc_u" ? "100%" : "40%" }};
    }
    .region-btn-eu {
        opacity: {{ session("region.code") == "pal" ? "100%" : "40%" }};
    }
    .region-btn-jp {
        opacity: {{ session("region.code") == "ntsc_j" ? "100%" : "40%" }};
    }
    .region-btn-pa {
        opacity: {{ session("region.code") == "pa" ? "100%" : "40%" }};
    }
</style>
