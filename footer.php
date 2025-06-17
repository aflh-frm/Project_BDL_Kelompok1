<script>
function showCreate(type) {
    const url = new URL(window.location.href);
    url.searchParams.set('action', 'create');
    url.searchParams.set('createtype', type);
    window.location.href = url.toString();
}
</script>
</body>
</html>