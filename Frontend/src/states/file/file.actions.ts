export class FetchFiles {
  static readonly type = "[File] Fetch files";

  constructor(public readonly directoryId: string) {}
}

export class CreateFile {
  static readonly type = "[File] Create file";

  constructor(public readonly file: File) {}
}

export class DeleteFile {
  static readonly type = "[File] Delete file";

  constructor(public readonly id: string) {}
}
