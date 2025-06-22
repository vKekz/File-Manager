export class CreateFile {
  static readonly type = "[File] Create file";

  constructor(public readonly file: File) {}
}

export class DeleteFile {
  static readonly type = "[File] Delete file";

  constructor(public readonly id: string) {}
}
