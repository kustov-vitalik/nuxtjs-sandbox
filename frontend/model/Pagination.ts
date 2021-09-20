export enum Direction {
  ASC = "ASC",
  DESC = "DESC"
}

export class SortOrder {
  private readonly _property: string;
  private readonly _direction: Direction;


  get property(): string {
    return this._property;
  }

  get direction(): Direction {
    return this._direction;
  }

  constructor(property: string, direction: Direction) {
    this._property = property;
    this._direction = direction;
  }

  public toString(): string {
    return this.property + '.' + this.direction.toString();
  }

  static ofQueryParam(sortOrderParam: string): SortOrder {
    let parts = sortOrderParam.split('.').map(value => value.trim()).filter(value => value != '');
    if (parts.length === 2) {
      return new SortOrder(parts[0].toLowerCase(), parts[1].toUpperCase() === 'ASC' ? Direction.ASC : Direction.DESC);
    } else if (parts.length === 1) {
      return new SortOrder(parts[0].toLowerCase(), Direction.ASC);
    } else {
      throw new Error(`Bad sort order definition: ${sortOrderParam}`);
    }
  }
}

export class Sort {
  private readonly _orders: SortOrder[];

  public static ofQueryParam(sort: string): Sort {
    let sortOrders: SortOrder[] = [];
    let sortOrderStrings = sort.split(':');
    if (sortOrderStrings.length > 0) {
      sortOrders = sortOrderStrings.map(sortOrderParam => SortOrder.ofQueryParam(sortOrderParam));
    }
    return new Sort(sortOrders);

  }

  constructor(orders: SortOrder[]) {
    this._orders = orders;
  }


  get orders(): SortOrder[] {
    return this._orders;
  }

  public toString(): string {
    return this.orders.join(':');
  }
}


export default class Pageable {
  get sort(): Sort {
    return this._sort;
  }

  get size(): number {
    return this._size;
  }

  get page(): number {
    return this._page;
  }

  private readonly _page: number;
  private readonly _size: number;
  private readonly _sort: Sort;

  constructor(page: number, size: number, sort: Sort) {
    this._page = page;
    this._size = size;
    this._sort = sort;
  }

  public static ofCurrentQueryString(defaultPage: number, defaultSize: number, defaultSort: Sort): Pageable {
    let query = (new URL(document.location.toString())).searchParams;

    let page: number = query.has('page') ? parseInt(<string>query.get('page')) : defaultPage;
    let size: number = query.has('size') ? parseInt(<string>query.get('size')) : defaultSize;
    let sort: Sort = query.has('sort') ? Sort.ofQueryParam(<string>query.get('sort')) : defaultSort;

    return new Pageable(page, size, sort);
  }

  public toQueryString(): string {

    let query = new URL(document.location.toString()).searchParams;
    query.set('page', this.page.toString());
    query.set('size', this.size.toString());
    query.set('sort', this.sort.toString());

    return query.toString();
  }
}

export class PageableResult<T> {
  private readonly _resultCount: number;
  private readonly _results: T[];
  private readonly _pageable: Pageable;

  constructor(resultCount: number, results: T[], pageable: Pageable) {
    this._resultCount = resultCount;
    this._results = results;
    this._pageable = pageable;
  }

  get resultCount(): number {
    return this._resultCount;
  }

  get results(): T[] {
    return this._results;
  }

  get pageable(): Pageable {
    return this._pageable;
  }

  static empty(): PageableResult<any> {
    return new PageableResult<any>(0, [], new Pageable(1, 10, new Sort([])));
  }
}


