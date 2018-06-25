<p>
    <a href="{{ $url }}" target="_blank">
        {{ preg_replace('#^[a-z]+?://([a-z0-9.-]+)(/.+)?#i', '\1', $url) }}
    </a>
</p>