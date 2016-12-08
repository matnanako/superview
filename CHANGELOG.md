# Changelog

All Notable changes to `superview` will be documented in this file.

Updates should follow the [Keep a CHANGELOG](http://keepachangelog.com/) principles.

## 1.0.1 - 2016-12-6

### Added
- SuperView增加page方法;
- Category增加categoryUrl方法;


### Fixed
- 修复SuperView::get($model)方法因为单例模式会被后面覆盖的问题, 将每个`$model`绑定一个单例;

## 1.0 - 2016-12-2

### Added
- SuperView增加page方法;
- Category下的children, finalChildren方法参数增加limit;


### Fixed
- 去除所有的page参数;
- 修改Utils下page方法名为renderPage;
- 修改Tag下的index方法参数顺序;
- 修改Content下的rank方法参数顺序;
- 修改Content下的top, good, firsttitle方法参数顺序;
- 修改所有limit默认为0, 即不限制;



## NEXT - YYYY-MM-DD

### Added
- Nothing

### Deprecated
- Nothing

### Fixed
- Nothing

### Removed
- Nothing

### Security
- Nothing
