export class FetchFiles {
  static readonly type = "[File] Fetch files";

  constructor(public readonly directoryId: string) {}
}

export class UploadFile {
  static readonly type = "[File] Upload file";

  constructor(public readonly file: File) {}
}
