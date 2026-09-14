---
title: larena/dataview
description: Нормализованное описание запросов и представлений данных.
tags: [larena-dataview, records, filter]
translation_key: larena.package.dataview
---

# `larena/dataview`

> **Для кого:** разработчик списков и выборок. **Результат:** подготовить
> детерминированный descriptor для UI. **Источник:** package README, Specs и Root
> tests. **Статус:** normalization and table/query foundation.

Dataview владеет описанием колонок, фильтров, сортировки, страниц и выбранного
вида результата. Текущая точка входа —
`Larena\Dataview\Runtime\DataviewDescriptorNormalizer`.

Чтобы подготовить список записей, сформируйте descriptor колонок, фильтра,
сортировки и пагинации, затем передайте нормализованный результат владельцу UI.

- Composer: `larena/dataview`.
- Владелец: normalized query and view descriptor.
- Зрелость: table/query foundation.

Пакет зависит от Core и Property. Он нормализует descriptor; выполнение прав и
хранение данных остаются у Access и Storage.

## Проверка

Root tests подтверждают сохранённый запрос, пагинацию, поиск и фильтрацию в
принятом table path.

## Ограничение

Реестр перечисляет шесть типов представлений, однако текущие доказательства не
подтверждают шесть полноценных UI. Используйте только принятый путь конкретной
сборки.
