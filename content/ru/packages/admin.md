---
title: larena/admin
description: Оркестрация защищённой административной поверхности.
tags: [larena-admin, admin, form, list]
translation_key: larena.package.admin
---

# `larena/admin`

> **Для кого:** разработчик admin-функций. **Результат:** собрать экран из
> владельцев данных, прав и UI. **Источник:** package README, Specs и Root
> browser evidence. **Статус:** bounded developer surface.

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

Текущий presenter поддерживает один объявленный путь и отклоняет остальное.
Это не универсальный конструктор административных страниц.

## Композиционный контейнер

Admin предоставляет защищённую оболочку и адаптер страницы входа. Для
`/admin/login` он связывает Auth presenter, Layout resolver и Smart backend
renderers. Blade остаётся тонким контейнером серверного HTML. Остальные экраны
переносятся отдельными вертикальными срезами после собственной приёмки.
