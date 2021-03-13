<nav class="navbar navbar-expand-lg navbar-dark bg-dark">
    <div class="container ">
      <a class="navbar-brand" href="{{ route('home') }}">Weibo App</a>
      <ul class="navbar-nav justify-content-end">
        {{-- 用户已经登录 --}}
        @if (Auth::check())
          <li class="nav-item"><a class="nav-link" href="#">用户列表</a></li>
          <li class="nav-item dropdown">
            <a class="nav-link dropdown-toggle" href="#" id="navbarDropdown" role="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                {{-- 登录用户的名称 --}}
              {{ Auth::user()->name }}
            </a>
            <div class="dropdown-menu" aria-labelledby="navbarDropdown">
                {{-- 跳转用户的个人信息页面 --}}
              <a class="dropdown-item" href="{{ route('users.show', Auth::user()) }}">个人中心</a>
              <a class="dropdown-item" href="#">编辑资料</a>
              <div class="dropdown-divider"></div>
              <a class="dropdown-item" id="logout" href="#">
                  {{-- 用户退出表单提交 --}}
                <form action="{{ route('logout') }}" method="POST">
                    {{-- 防止CSRF攻击 --}}
                  {{ csrf_field() }}
                    {{-- 浏览器模拟delete请求 --}}
                  {{ method_field('DELETE') }}
                  <button class="btn btn-block btn-danger" type="submit" name="button">退出</button>
                </form>
              </a>
            </div>
          </li>
        {{-- 用户没有登录 --}}
        @else
          <li class="nav-item"><a class="nav-link" href="{{ route('help') }}">帮助</a></li>
          <li class="nav-item" ><a class="nav-link" href="{{ route('login') }}">登录</a></li>
        @endif
      </ul>
    </div>
  </nav>
