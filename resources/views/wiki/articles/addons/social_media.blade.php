<ul>
    @isset($facebook)
        <li>
            <a href="https://facebook.com/{{ $facebook }}" target="_blank">Facebook</a>
        </li>
    @endisset
    @isset($twitter)
        <li>
            <a href="https://twitter.com/{{ $twitter }}" target="_blank">Twitter</a>
        </li>
    @endisset
    @isset($instagram)
        <li>
            <a href="https://www.instagram.com/{{ $instagram }}" target="_blank">Instagram</a>
        </li>
    @endisset
    @isset($youtube)
        <li>
            <a href="https://www.youtube.com/channel/{{ $youtube }}" target="_blank">YouTube</a>
        </li>
    @endisset
</ul>
