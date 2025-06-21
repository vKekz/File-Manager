export class FetchStorage {
  static readonly type = "[Storage] Fetch storage";

  constructor() {}
}

export class SelectStorage {
  static readonly type = "[Storage] Select storage";

  constructor(public readonly directoryId: string) {}
}

export class ResetResponse {
  static readonly type = "[Storage] Reset response";

  constructor() {}
}
