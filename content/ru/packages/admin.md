---
title: larena/admin
description: Оркестрация защищённой административной поверхности.
tags: [larena-admin, admin, form, list]
translation_key: larena.package.admin
---

# `larena/admin`

> **Для кого:** разработчик admin-функций. **Результат:** собрать экран из
> владельцев данных, прав и UI. **Источник:** package README, Specs и Root
> browser evidence. **Статус:** проверенный ограниченный редактор композиции.

Admin собирает навигацию, контроллер, presenter и package contributions. Он не
забирает семантику Auth, Access, Storage, Property, Dataview, Layout или UI.

- Composer: `larena/admin`.
- Владелец: protected admin composition.
- Зрелость: один bounded collection-screen path.

Текущие точки принятого Minimal CMS пути —
`Larena\Admin\Http\Controllers\MinimalCmsController` и
`Larena\Admin\Runtime\MinimalCmsSmartPagePresenter`.

Пакет зависит от остальных одиннадцати пакетов, что отражает роль оркестратора.

## Проверка

Root browser и integration evidence подтверждают CRUD, контекст, права и один
явно объявленный collection-screen. Прямой URL без разрешения должен дать
отказ или перенаправление на вход.

## Ограничение

Текущий редактор работает только с зарегистрированными сервером целями и
каталогами. Он поддерживает две демонстрационные страницы, вложенные экземпляры,
публикацию и откат, но ещё не является универсальным конструктором всех
административных страниц.

## Композиционный контейнер

Admin предоставляет защищённую оболочку и адаптер страницы входа. Для
`/admin/login` он связывает Auth presenter, Layout resolver и Smart backend
renderers. Blade остаётся тонким контейнером серверного HTML. Остальные экраны
переносятся отдельными вертикальными срезами после собственной приёмки.

Для `/admin/composition-editor` Admin также предоставляет presenter полей
Framework и локализованную оболочку. Значения и команды по-прежнему проверяет
Layout; Admin не становится владельцем макета, содержимого или публикации.
