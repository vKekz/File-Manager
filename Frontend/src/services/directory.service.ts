import { Inject, Injectable } from "@angular/core";
import { API_URL } from "../app/app.config";
import { HttpClient } from "@angular/common/http";
import { API_ROUTES } from "../constants/route.constants";
import { DirectoryDto, DirectoryDtoWithChildren } from "../dtos/directory.dto";
import { firstValueFrom } from "rxjs";
import { ApiResponse } from "../contracts/api.response";
import { DeleteDirectoryResponse } from "../contracts/delete-directory.response";

@Injectable({ providedIn: "root" })
export class DirectoryService {
  private readonly endpoint: string;

  constructor(
    @Inject(API_URL) apiUrl: string,
    private readonly httpClient: HttpClient
  ) {
    this.endpoint = `${apiUrl}/${API_ROUTES.directory}`;
  }

  public getDirectoryWithChildren(id: string) {
    const route = `${this.endpoint}?id=${id}`;
    const request = this.httpClient.get<DirectoryDtoWithChildren>(route);
    return firstValueFrom(request);
  }

  public createDirectory(name: string, parentId: string) {
    const route = `${this.endpoint}`;
    const payload = {
      name: name,
      parentId: parentId,
    } as const;
    const request = this.httpClient.post<DirectoryDto | ApiResponse>(route, payload);
    return firstValueFrom(request);
  }

  public deleteDirectory(id: string) {
    const route = `${this.endpoint}?id=${id}`;
    const request = this.httpClient.delete<DeleteDirectoryResponse | ApiResponse>(route);
    return firstValueFrom(request);
  }
}
