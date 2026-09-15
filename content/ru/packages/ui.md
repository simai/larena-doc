---
title: larena/ui
description: Реестр Smart-компонентов, разрешение представлений и frontend lock.
tags: [larena-ui, smart, component]
translation_key: larena.package.ui
---

# `larena/ui`

> **Для кого:** разработчик интерфейса. **Результат:** разрешить только
> зарегистрированный Smart и его зафиксированные ресурсы. **Источник:** package
> README, manifests и Root lock. **Статус:** developer foundation с точным
> frontend runtime lock.

UI владеет реестром Smart-компонентов, безопасным разрешением view и связью с
зафиксированным frontend artifact. Основные точки текущего среза —
`Larena\Ui\Runtime\SmartManager` и `FrontendRuntimeLock`.

- Composer: `larena/ui`.
- Владелец: Smart registry, rendering and frontend lock.
- Зрелость: developer foundation с exact runtime identity.

Пакет зависит от Core и Dataview. Тип компонента, props и ресурсы должны быть
объявлены в реестре; произвольный класс или путь шаблона не является допустимым
компонентом.

## Проверка

Root exact-release validator сравнивает UI revision с lock, а integration test
проверяет Smart render. Совпадение lock подтверждает идентичность ресурсов, а
не полноту всех интерфейсных состояний.

## Роль в композиции

UI владеет реестром допустимых компонентов, Smart backend renderers, вариантами,
именованными слотами и ресурсами. UI не хранит дерево страницы. Точная поставка
Framework проверяется по Git-ревизиям, manifest и digest; сгенерированные
артефакты вручную не редактируются.
