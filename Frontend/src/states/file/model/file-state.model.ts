import { FileDto } from "../../../dtos/file.dto";
import { ApiResponse } from "../../../contracts/api.response";

export interface FileStateModel {
  files: FileDto[];
  response?: ApiResponse;
}
