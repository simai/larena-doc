---
title: larena/auth
description: Личности, вход, сессии, восстановление и MFA администратора.
tags: [larena-auth, login, user, mfa]
translation_key: larena.package.auth
---

# `larena/auth`

> **Для кого:** разработчик защищённых поверхностей. **Результат:** использовать
> личность и сессию без переноса прав в Auth. **Источник:** package README и
> developer docs. **Статус:** durable backend runtime, без production claim.

Auth владеет личностями администратора, парольными хешами, сессиями,
восстановлением, приглашением и TOTP MFA. Access отдельно решает, что этой
личности разрешено.

- Composer: `larena/auth`.
- Владелец: identity, session, recovery and MFA.
- Зрелость: durable backend runtime без production claim.

## Вход

Маршруты `/admin/login` и `/admin/logout` включаются явно через
`LARENA_AUTH_ADMIN_ENTRY_ROUTES=true`. Первый setup доступен при пустой таблице
личностей и может быть отдельно отключён через
`LARENA_AUTH_BROWSER_SETUP=false`.

Рядом с формой входа Auth обслуживает страницы запроса и сброса пароля
(`/admin/password/forgot`), принятия приглашения (`/admin/activate/…`), смены
пароля по требованию администратора и подтверждения пароля перед
чувствительным действием. Пароль — от 12 до 72 байт, с хотя бы одним
неалфавитно-цифровым символом.

Обязательность двухфакторной аутентификации задаёт `LARENA_AUTH_MFA_REQUIRED`:
`none` (по умолчанию), `all` или список кодов ролей через запятую. Пока
обязательная настройка или смена пароля не завершены, администратор не
попадает в административную часть.

## Зависимости и использование

Auth зависит только от `larena/core` и Illuminate 13. Экраны пользователей и
безопасности учётной записи, а также страница `/admin/setup` находятся в
`larena/admin`; управление администраторами — в `larena/access`. Приложение получает уже
проверенную persistent session через package service provider. REST владеет
общими HTTP-гейтами, CSRF, rate limit и idempotency.

## Ограничение

Доставка recovery и invitation требует явно подключённого адаптера. Защита MFA
secret также должна быть связана приложением; встроенный fallback отказывает.

## Страница входа

Auth владеет смыслом входа, полями, ошибками, rate limit, MFA и сессией, но не
деревом страницы. `/admin/login` собирается из пакетных артефактов Layout через
Framework Recipe. CSRF, пароль, MFA proof, session ID и персонализированные
данные существуют только в текущем запросе и не попадают в общий snapshot.
