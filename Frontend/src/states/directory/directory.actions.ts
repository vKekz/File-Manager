export class FetchDirectories {
  static readonly type = "[Directory] Fetch directories";

  constructor() {}
}

export class SelectDirectory {
  static readonly type = "[Directory Select directory";

  constructor(public readonly id: string) {}
}

export class CreateDirectory {
  static readonly type = "[Directory] Create directory";

  constructor(public readonly name: string) {}
}

export class DeleteDirectory {
  static readonly type = "[Directory] Delete directory";

  constructor(public readonly id: string) {}
}

export class ResetResponse {
  static readonly type = "[Directory] Reset response";

  constructor() {}
}
