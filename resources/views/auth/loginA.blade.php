<html>
    <form action="/login" method="POST">
        @csrf
        username: <input type="text" name="username"><br>
        password: <input type="password" name="password"><br>
        <button type="submit">Login</button>
    </form>
</html>