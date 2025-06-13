import { Inject, Injectable } from "@angular/core";
import { API_URL } from "../app/app.config";
import { HttpClient } from "@angular/common/http";
import { API_ROUTES } from "../constants/route.constants";
import { firstValueFrom } from "rxjs";
import { FileDto } from "../dtos/file.dto";
import { ApiResponse } from "../contracts/api.response";
import { DeleteFileResponse } from "../contracts/delete-file.response";

@Injectable({ providedIn: "root" })
export class FileService {
  private readonly endpoint: string;

  constructor(
    @Inject(API_URL) apiUrl: string,
    private readonly httpClient: HttpClient
  ) {
    this.endpoint = `${apiUrl}/${API_ROUTES.file}`;
  }

  public downloadFile(id: string) {
    const route = `${this.endpoint}/download?id=${id}`;
    const options = {
      responseType: "blob" as "json",
    };
    return firstValueFrom(this.httpClient.get<Blob>(route, options));
  }

  public getDirectoryFiles(directoryId: string) {
    const route = `${this.endpoint}?directoryId=${directoryId}`;
    const request = this.httpClient.get<FileDto[]>(route);
    return firstValueFrom(request);
  }

  public createFile(directoryId: string, file: File) {
    const payload = new FormData();
    payload.append("directoryId", directoryId);
    payload.append("file", file);

    const route = `${this.endpoint}`;
    const request = this.httpClient.post<FileDto | ApiResponse>(route, payload);
    return firstValueFrom(request);
  }

  public deleteFile(id: string) {
    const route = `${this.endpoint}?id=${id}`;
    const request = this.httpClient.delete<DeleteFileResponse | ApiResponse>(route);
    return firstValueFrom(request);
  }
}
