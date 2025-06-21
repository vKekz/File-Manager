export class CreateDirectory {
  static readonly type = "[Directory] Create directory";

  constructor(public readonly name: string) {}
}

export class DeleteDirectory {
  static readonly type = "[Directory] Delete directory";

  constructor(public readonly id: string) {}
}
