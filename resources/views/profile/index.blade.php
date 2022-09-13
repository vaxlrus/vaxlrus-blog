<form action="{{ route('deleteUserProfile') }}" method="POST">
    @csrf
    @method('DELETE')

    <button type="submit" onclick="confirm('После удаления аккаунта вы сможете восстановить его до {{ $profileRecoverPeriod }}')">Delete Account</button>
</form>
