---
title: larena/setting
description: Схемы настроек, области значений и контролируемое применение изменений.
tags: [larena-setting, settings]
translation_key: larena.package.setting
---

# `larena/setting`

> **Для кого:** разработчик функций и админ-интерфейса. **Результат:** выбрать
> правильный контракт настройки. **Источник:** package README, Specs и Root
> smokes. **Статус:** developer-testable foundation.

Setting владеет машиночитаемыми определениями настроек, разрешением значений,
pending changes, preview и контролируемым применением. Пакет содержит durable
значения сайта в `larena_setting_values` и операции `setting.site.read` и
`setting.site.write`.

- Composer: `larena/setting`.
- Владелец: определения, значения и guarded apply.
- Зрелость: developer-testable foundation.

## Зависимости и точка входа

Пакет зависит от `larena/core`, Illuminate 13 и `ramsey/uuid`. Для текущего
site-setting среза используйте package-owned `SiteSettingStore`; не обращайтесь
к таблице напрямую.

## Проверка

Root quality gate включает smokes схем, формы, guarded apply и persistence.
Перед применением изменения сначала используйте read model и preview; точная
команда зависит от активного launch contract.

## Ограничение

Это ограниченный foundation, а не универсальная production-платформа настроек.
Выбор страницы и файла остаётся у их владельцев.

## Настройки оформления

Setting предоставляет значения и наследование для темы, дизайн-пакета и
разрешённых вариантов компонентов. Настройка выбирает вариант при resolution,
но не создаёт копию страницы. Digest фактически применённых настроек входит в
ключ compiled snapshot.
